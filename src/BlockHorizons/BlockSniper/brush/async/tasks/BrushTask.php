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
use BlockHorizons\BlockSniper\parser\IdMap;
use BlockHorizons\BlockSniper\revert\AsyncRevert;
use BlockHorizons\BlockSniper\session\PlayerSession;
use BlockHorizons\BlockSniper\session\Session;
use BlockHorizons\BlockSniper\session\SessionManager;
use BlockHorizons\BlockSniper\task\CooldownBarTask;
use Generator;
use InvalidArgumentException;
use pocketmine\block\Block;
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

	/** @var Shape */
	private $shape;
	/** @var string[] */
	private $chunks;
	/** @var Type */
	private $type;
	/** @var Vector2[] */
	private $plotPoints;
	/** @var float */
	private $startTime;
	/** @var BrushProperties */
	private $brushProperties;
	/** @var string[] */
	private $idMap;

	public function __construct(Brush $brush, Session $session, Shape $shape, Type $type, World $world, array $plotPoints = []){
		$chunks = $shape->getTouchedChunks($world);
		$this->storeLocal("", [$world, $session, $chunks, $brush, 0]);
		$this->shape = $shape;
		$this->type = $type;
		$this->brushProperties = $brush;
		$this->chunks = $chunks;
		$this->plotPoints = $plotPoints;
		$this->startTime = microtime(true);
		$this->idMap = serialize(IdMap::$ids);
	}

	public function onRun() : void{
		IdMap::$ids = unserialize($this->idMap);
		$type = $this->type;
		$type->setBrushBlocks($this->brushProperties->getBrushBlocks());
		$shape = $this->shape;
		$plotPoints = (array) $this->plotPoints;

		// Publish progress immediately so that there will be no delay until the progress indicator appears.
		$this->publishProgress(0);

		$chunks = (array) $this->chunks;
		foreach($chunks as $hash => $data){
			$chunks[$hash] = FastChunkSerializer::deserialize($data);
		}

		/** @var Chunk[] $chunks */
		$manager = new BlockSniperChunkManager();
		foreach($chunks as $chunk){
			$manager->setChunk($chunk->getX(), $chunk->getZ(), $chunk);
		}
		$type->setBlocksInside($this->blocks($shape, $chunks))->setChunkManager($manager);

		foreach($type->fillShape($plotPoints) as $b){
		}

		// Publish progress for 21 so that the user gets a message 'Done'.
		$this->publishProgress(21);
		$this->setResult($chunks);
	}

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

			[$posX, $posY, $posZ] = [(int) $vector3->x & 0x0f, (int) $vector3->y, (int) $vector3->z & 0x0f];
			$combinedValue = $chunk->getFullBlock($posX, $posY, $posZ);
			$block = Block::get($combinedValue >> 4, $combinedValue & 0xf);
			$block->setComponents($vector3->x, $vector3->y, $vector3->z);

			++$i;
			if($i === $percentageBlocks){
				$this->publishProgress((int) ceil($i / $blockCount * 20));
				$percentageBlocks += $blocksPerPercentage;
			}
			yield $block;
		}
	}

	public function onProgressUpdate($progress) : void{
		/**
		 * @var World   $world
		 * @var Session $session
		 */
		[$world, $session, $chunks, $brush, $lastProgress] = $this->fetchLocal("");
		if($lastProgress !== $progress){
			if($session instanceof PlayerSession){
				$player = $session->getSessionOwner()->getPlayer();
				$world->addSound($player, new ClickSound(), [$player]);
			}
		}
		$this->storeLocal("", [$world, $session, $chunks, $brush, $progress]);

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
		/**
		 * @var World   $world
		 * @var Session $session
		 * @var Brush   $brush
		 */
		[$world, $session, $undoChunks, $brush] = $this->fetchLocal("");

		foreach($undoChunks as &$undoChunk){
			$undoChunk = FastChunkSerializer::deserialize($undoChunk);
		}

		foreach($chunks as $hash => $chunk){
			$world->setChunk($chunk->getX(), $chunk->getZ(), $chunk, false);
		}

		if(($player = Server::getInstance()->getPlayer($session->getSessionOwner()->getName())) !== null){
			$loader->getScheduler()->scheduleDelayedRepeatingTask(new CooldownBarTask($loader, $brush, $player), 1, 3);
		}
		SessionManager::getPlayerSession($player)->getRevertStore()->saveUndo(new AsyncRevert($chunks, $undoChunks, $world));
		$brush->emitSound($player);
	}
}