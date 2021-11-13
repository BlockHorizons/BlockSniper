<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\brush\async\tasks;

use BlockHorizons\BlockSniper\brush\async\BlockSniperChunkManager;
use BlockHorizons\BlockSniper\brush\Brush;
use BlockHorizons\BlockSniper\brush\BrushProperties;
use BlockHorizons\BlockSniper\brush\Shape;
use BlockHorizons\BlockSniper\brush\Type;
use BlockHorizons\BlockSniper\data\Translation;
use BlockHorizons\BlockSniper\Loader;
use BlockHorizons\BlockSniper\revert\AsyncRevert;
use BlockHorizons\BlockSniper\session\owner\ISessionOwner;
use BlockHorizons\BlockSniper\session\PlayerSession;
use BlockHorizons\BlockSniper\session\Session;
use BlockHorizons\BlockSniper\session\SessionManager;
use BlockHorizons\BlockSniper\task\CooldownBarTask;
use Generator;
use InvalidArgumentException;
use pocketmine\block\Block;
use pocketmine\block\BlockFactory;
use pocketmine\math\Vector2;
use pocketmine\scheduler\AsyncTask;
use pocketmine\Server;
use pocketmine\utils\TextFormat;
use pocketmine\world\format\Chunk;
use pocketmine\world\format\io\FastChunkSerializer;
use pocketmine\world\sound\ClickSound;
use pocketmine\world\World;
use function round;
use function str_repeat;

class BrushTask extends AsyncTask{

	private const KEY_WORLD = "world";
	private const KEY_SESSION = "session";
	private const KEY_CHUNKS = "chunks";
	private const KEY_BRUSH = "brush";
	private const KEY_PROGRESS = "progress";

	/** @var Shape */
	private $shape;
	/** @var string[] */
	private $chunks;
	/** @var Type */
	private $type;
	/** @var Vector2[][] */
	private $plotPoints;
	/** @var float */
	private $startTime;
	/** @var BrushProperties */
	private $brushProperties;
	/** @var string */
	private $idMap;

	/**
	 * @param Vector2[][] $plotPoints
	 * @phpstan-param Session<ISessionOwner> $session
	 */
	public function __construct(Brush $brush, Session $session, Shape $shape, Type $type, World $world, array $plotPoints = []){
		$chunks = $shape->getTouchedChunks($world);
		$this->storeLocal(self::KEY_WORLD, $world);
		$this->storeLocal(self::KEY_SESSION, $session);
		$this->storeLocal(self::KEY_CHUNKS, $chunks);
		$this->storeLocal(self::KEY_BRUSH, $brush);
		$this->storeLocal(self::KEY_PROGRESS, 0);
		$this->shape = $shape;
		$this->type = $type;
		$this->brushProperties = $brush;
		$this->chunks = $chunks;
		$this->plotPoints = $plotPoints;
		$this->startTime = microtime(true);
	}

	public function onRun() : void{
		$type = $this->type;
		$type->setBrushBlocks($this->brushProperties->getBrushBlocks());
		$shape = $this->shape;
		$plotPoints = (array) $this->plotPoints;

		// Publish progress immediately so that there will be no delay until the progress indicator appears.
		$this->publishProgress(0);

		$chunks = [];
		foreach((array) $this->chunks as $hash => $data){
			$chunks[$hash] = FastChunkSerializer::deserializeTerrain($data);
		}

		$manager = new BlockSniperChunkManager(World::Y_MIN, World::Y_MAX);
		foreach($chunks as $chunkHash => $chunk){
			World::getXZ($chunkHash, $chunkX, $chunkZ);
			$manager->setChunk($chunkX, $chunkZ, $chunk);
		}
		$type->setBlocksInside($this->blocks($shape, $chunks))->setChunkManager($manager);

		foreach($type->fillShape($plotPoints) as $b){
		}

		// Publish progress for 21 so that the user gets a message 'Done'.
		$this->publishProgress(21);
		$this->setResult($chunks);
	}

	/**
	 * @param Chunk[] $chunks
	 *
	 * @phpstan-return \Generator<int, Block, void, void>
	 */
	private function blocks(Shape $shape, array $chunks) : Generator{
		$blockCount = $shape->getBlockCount();
		$blocksPerPercentage = (int) round($blockCount / 100);
		$percentageBlocks = $blocksPerPercentage;

		$i = 0;
		foreach($shape->getVectors() as $vector3){
			$index = World::chunkHash($vector3->x >> 4, $vector3->z >> 4);
			if(!isset($chunks[$index])){
				throw new InvalidArgumentException("chunk not found for block");
			}
			/** @var Chunk $chunk */
			$chunk = $chunks[$index];

			[$posX, $posY, $posZ] = [$vector3->x & 0x0f, $vector3->y, $vector3->z & 0x0f];
			$combinedValue = $chunk->getFullBlock($posX, $posY, $posZ);
			$block = BlockFactory::getInstance()->fromFullBlock($combinedValue);
			$block->getPosition()->x = $vector3->x;
			$block->getPosition()->y = $vector3->y;
			$block->getPosition()->z = $vector3->z;

			++$i;
			if($i === $percentageBlocks){
				$this->publishProgress((int) ceil($i / $blockCount * 20));
				$percentageBlocks += $blocksPerPercentage;
			}
			yield $block;
		}
	}

	public function onProgressUpdate($progress) : void{
		$lastProgress = $this->fetchLocal(self::KEY_PROGRESS);
		$world = $this->fetchLocal(self::KEY_WORLD);
		$session = $this->fetchLocal(self::KEY_SESSION);
		if($lastProgress !== $progress){
			if($session instanceof PlayerSession){
				$player = $session->getSessionOwner()->getPlayer();
				$world->addSound($player->getPosition(), new ClickSound(), [$player]);
			}
		}
		$this->storeLocal(self::KEY_PROGRESS, $progress);

		if($progress >= 21){
			$duration = round(microtime(true) - $this->startTime, 2);
			$session->getSessionOwner()->sendMessage(TextFormat::GREEN . Translation::get(Translation::BRUSH_STATE_DONE) . " ($duration seconds)");

			return;
		}
		$session->getSessionOwner()->sendMessage(TextFormat::GREEN . str_repeat("|", $progress) . TextFormat::RED . str_repeat("|", 20 - $progress));
	}

	public function onCompletion() : void{
		/** @var Loader $loader */
		$loader = Server::getInstance()->getPluginManager()->getPlugin("BlockSniper");

		/** @var Chunk[] $chunks */
		$chunks = $this->getResult();
		/** @var World $world */
		$world = $this->fetchLocal(self::KEY_WORLD);
		/** @var Session<ISessionOwner> $session */
		$session = $this->fetchLocal(self::KEY_SESSION);
		$undoChunks = $this->fetchLocal(self::KEY_CHUNKS);
		/** @var Brush $brush */
		$brush = $this->fetchLocal(self::KEY_BRUSH);

		foreach($undoChunks as &$undoChunk){
			$undoChunk = FastChunkSerializer::deserializeTerrain($undoChunk);
		}

		foreach($chunks as $hash => $chunk){
			World::getXZ($hash, $chunkX, $chunkZ);
			$world->setChunk($chunkX, $chunkZ, $chunk);
		}

		if(($player = Server::getInstance()->getPlayerExact($session->getSessionOwner()->getName())) !== null){
			$loader->getScheduler()->scheduleDelayedRepeatingTask(new CooldownBarTask($loader, $brush, $player), 1, 3);
		}
		SessionManager::getPlayerSession($player)->getRevertStore()->saveUndo(new AsyncRevert($chunks, $undoChunks, $world));
		$brush->emitSound($player);
	}
}