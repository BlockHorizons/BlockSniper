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
use pocketmine\math\Vector3;
use pocketmine\scheduler\AsyncTask;
use pocketmine\Server;
use pocketmine\utils\TextFormat;
use pocketmine\world\format\Chunk;
use pocketmine\world\format\io\FastChunkSerializer;
use pocketmine\world\sound\FizzSound;
use pocketmine\world\World;

class PasteTask extends AsyncTask{

	/** @var string */
	private $file;
	/** @var Vector3 */
	private $center;
	/** @var string[] */
	private $chunks;
	/** @var string */
	private $playerName;

	public function __construct(string $file, Vector3 $center, array $chunks, string $playerName){
		$this->storeLocal("", [$chunks, microtime(true)]);
		$this->file = $file;
		$this->center = $center;
		$this->chunks = $chunks;
		$this->playerName = $playerName;
	}

	public function onRun() : void{
		$chunks = (array) $this->chunks;
		foreach($chunks as $hash => $data){
			$chunks[$hash] = FastChunkSerializer::deserialize($data);
		}

		$center = $this->center;

		$schematic = new Schematic();
		$schematic->parse($this->file);

		$width = $schematic->getWidth();
		$length = $schematic->getLength();
		$baseWidth = $center->x - (int) ($width / 2);
		$baseLength = $center->z - (int) ($length / 2);

		/** @var Chunk[] $chunks */
		$manager = new BlockSniperChunkManager();
		foreach($chunks as $chunk){
			$manager->setChunk($chunk->getX(), $chunk->getZ(), $chunk);
		}

		$processedBlocks = 0;
		/** @var Block[] $blocksInside */
		foreach($schematic->blocks() as $block){
			if($block->getId() === Block::AIR){
				continue;
			}
			$tempX = $baseWidth + $block->x;
			$tempY = $center->y + $block->y;
			$tempZ = $baseLength + $block->z;
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
		if(!($player = Server::getInstance()->getPlayer($this->playerName))){
			return;
		}

		[$undoChunks, $startTime] = $this->fetchLocal("");
		foreach($undoChunks as &$undoChunk){
			$undoChunk = FastChunkSerializer::deserialize($undoChunk);
		}

		$world = $player->getWorld();
		/** @var Chunk[] $chunks */
		$chunks = $this->getResult();

		if($world instanceof World){
			foreach($chunks as $hash => $chunk){
				$world->setChunk($chunk->getX(), $chunk->getZ(), $chunk, false);
			}
		}

		$duration = round(microtime(true) - $startTime, 2);
		$player->sendPopup(TextFormat::GREEN . Translation::get(Translation::BRUSH_STATE_DONE) . " ($duration seconds)");
		$player->getWorld()->addSound($player, new FizzSound(), [$player]);
		SessionManager::getPlayerSession($player)->getRevertStore()->saveUndo(new AsyncRevert($chunks, $undoChunks, $player->getWorld()));
	}
}