<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\brush\async\tasks;

use BlockHorizons\BlockSniper\brush\async\BlockSniperChunkManager;
use BlockHorizons\BlockSniper\Loader;
use BlockHorizons\BlockSniper\revert\async\AsyncUndo;
use BlockHorizons\BlockSniper\sessions\SessionManager;
use BlockHorizons\libschematic\Schematic;
use pocketmine\block\Block;
use pocketmine\level\format\Chunk;
use pocketmine\level\Level;
use pocketmine\math\Vector3;
use pocketmine\scheduler\AsyncTask;
use pocketmine\Server;

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
		$this->storeLocal($chunks);
		$this->file = $file;
		$this->center = $center;
		$this->chunks = $chunks;
		$this->playerName = $playerName;
	}

	public function onRun() : void{
		$chunks = (array) $this->chunks;
		foreach($chunks as $hash => $data){
			$chunks[$hash] = Chunk::fastDeserialize($data);
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
		foreach($chunks as $chunk) {
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
			$index = Level::chunkHash($tempX >> 4, $tempZ >> 4);

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

		$undoChunks = $this->fetchLocal();
		foreach($undoChunks as &$undoChunk){
			$undoChunk = Chunk::fastDeserialize($undoChunk);
		}

		$level = $player->getLevel();
		/** @var Chunk[] $chunks */
		$chunks = $this->getResult();

		if($level instanceof Level){
			foreach($chunks as $hash => $chunk){
				$level->setChunk($chunk->getX(), $chunk->getZ(), $chunk, false);
			}
		}

		SessionManager::getPlayerSession($player)->getRevertStore()->saveRevert(new AsyncUndo($chunks, $undoChunks, $this->playerName, $player->getLevel()->getId()));
	}
}