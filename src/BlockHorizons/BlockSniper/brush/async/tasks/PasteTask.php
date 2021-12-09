<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\brush\async\tasks;

use BlockHorizons\BlockSniper\brush\async\BlockSniperChunkManager;
use BlockHorizons\BlockSniper\data\Translation;
use BlockHorizons\BlockSniper\Loader;
use BlockHorizons\BlockSniper\revert\AsyncRevert;
use BlockHorizons\BlockSniper\session\SessionManager;
use BlockHorizons\libschematic\Schematic;
use pocketmine\block\Block;
use pocketmine\block\BlockLegacyIds;
use pocketmine\math\Vector3;
use pocketmine\scheduler\AsyncTask;
use pocketmine\Server;
use pocketmine\utils\TextFormat;
use pocketmine\world\format\Chunk;
use pocketmine\world\format\io\FastChunkSerializer;
use pocketmine\world\sound\FizzSound;
use pocketmine\world\World;
use function microtime;

class PasteTask extends AsyncTask{

	private const KEY_CHUNKS = "chunks";

	/** @var string */
	private $file;
	/** @var Vector3 */
	private $center;
	/** @var string[] */
	private $chunks;
	/** @var string */
	private $playerName;
	/** @var float */
	private $startTime;

	/**
	 * @param string[] $chunks
	 */
	public function __construct(string $file, Vector3 $center, array $chunks, string $playerName){
		$this->storeLocal(self::KEY_CHUNKS, $chunks);
		$this->file = $file;
		$this->center = $center;
		$this->chunks = $chunks;
		$this->playerName = $playerName;
		$this->startTime = microtime(true);
	}

	public function onRun() : void{
		$chunks = [];
		foreach((array) $this->chunks as $hash => $data){
			$chunks[$hash] = FastChunkSerializer::deserializeTerrain($data);
		}

		$center = $this->center;

		$schematic = new Schematic();
		$schematic->parse($this->file);

		$width = $schematic->getWidth();
		$length = $schematic->getLength();
		$baseWidth = $center->x - (int) ($width / 2);
		$baseLength = $center->z - (int) ($length / 2);

		/** @var Chunk[] $chunks */
		$manager = new BlockSniperChunkManager(World::Y_MIN, World::Y_MAX);
		foreach($chunks as $hash => $chunk){
			World::getXZ($hash, $chunkX, $chunkZ);
			$manager->setChunk($chunkX, $chunkZ, $chunk);
		}

		$processedBlocks = 0;
		foreach($schematic->blocks() as $block){
			if($block->getId() === BlockLegacyIds::AIR){
				continue;
			}
			$tempX = $baseWidth + $block->getPosition()->x;
			$tempY = $center->y + $block->getPosition()->y;
			$tempZ = $baseLength + $block->getPosition()->z;
			$index = World::chunkHash($tempX >> 4, $tempZ >> 4);

			if(isset($chunks[$index])){
				$manager->setBlockAt($tempX, $tempY, $tempZ, $block);
				$processedBlocks++;
			}
		}

		$this->setResult($chunks);
	}

	public function onCompletion() : void{
		/** @var Loader $loader */
		$loader = Server::getInstance()->getPluginManager()->getPlugin("BlockSniper");
		if(!$loader->isEnabled()){
			return;
		}
		if(!($player = Server::getInstance()->getPlayerExact($this->playerName))){
			return;
		}

		$undoChunks = $this->fetchLocal(self::KEY_CHUNKS);
		foreach($undoChunks as &$undoChunk){
			$undoChunk = FastChunkSerializer::deserializeTerrain($undoChunk);
		}

		$world = $player->getWorld();
		/** @var Chunk[] $chunks */
		$chunks = $this->getResult();

		if($world instanceof World){
			foreach($chunks as $hash => $chunk){
				World::getXZ($hash, $chunkX, $chunkZ);
				$world->setChunk($chunkX, $chunkZ, $chunk);
			}
		}

		$duration = round(microtime(true) - $this->startTime, 2);
		$player->sendPopup(TextFormat::GREEN . Translation::get(Translation::BRUSH_STATE_DONE) . " ($duration seconds)");
		$player->getWorld()->addSound($player->getPosition(), new FizzSound(), [$player]);
		SessionManager::getPlayerSession($player)->getRevertStore()->saveUndo(new AsyncRevert($chunks, $undoChunks, $player->getWorld()));
	}
}