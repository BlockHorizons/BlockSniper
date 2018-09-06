<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\brush\async\tasks;

use BlockHorizons\BlockSniper\brush\BaseType;
use BlockHorizons\BlockSniper\Loader;
use BlockHorizons\BlockSniper\revert\async\AsyncUndo;
use BlockHorizons\BlockSniper\sessions\SessionManager;
use BlockHorizons\libschematic\Schematic;
use pocketmine\block\Block;
use pocketmine\level\format\Chunk;
use pocketmine\level\Level;
use pocketmine\math\Vector3;
use pocketmine\Server;

class PasteTask extends AsyncBlockSniperTask{

	/** @var string */
	private $file = "";
	/** @var Vector3 */
	private $center = null;
	/** @var string[] */
	private $chunks = "";
	/** @var string */
	private $playerName = "";

	public function __construct(string $file, Vector3 $center, array $chunks, string $playerName){
		$this->file = $file;
		$this->center = $center;
		$this->chunks = serialize($chunks);
		$this->playerName = $playerName;
	}

	public function onRun() : void{
		$chunks = unserialize($this->chunks, ["allowed_classes" => [Chunk::class]]);
		$file = $this->file;
		$center = $this->center;

		$schematic = new Schematic($file);
		$schematic->decode();
		$schematic->fixBlockIds();
		$width = $schematic->getWidth();
		$length = $schematic->getLength();
		$height = $schematic->getHeight();

		$undoChunks = $chunks;

		$processedBlocks = 0;
		foreach($chunks as $hash => $data){
			$chunks[$hash] = Chunk::fastDeserialize($data);
		}
		/** @var Chunk[] $chunks */
		/** @var Block[] $blocksInside */
		$blocksInside = $schematic->getBlocks();
		$manager = BaseType::establishChunkManager($chunks);
		$i = 0;

		$baseWidth = $center->x - (int) ($width / 2);
		$baseLength = $center->z - (int) ($length / 2);

		foreach($blocksInside as $block){
			if($block->getId() === Block::AIR){
				continue;
			}
			$tempX = $baseWidth + $block->x;
			$tempY = $center->y + $block->y;
			$tempZ = $baseLength + $block->z;
			$index = Level::chunkHash($tempX >> 4, $tempZ >> 4);

			if(isset($chunks[$index])){
				$manager->setBlockIdAt($tempX, $tempY, $tempZ, $block->getId());
				$manager->setBlockDataAt($tempX, $tempY, $tempZ, $block->getDamage());
				$processedBlocks++;
			}

			if(++$i === (int) ($length * $width * $height / 100)){
				if($this->isAborted()){
					return;
				}
				$this->publishProgress(ceil($processedBlocks / ($length * $width * $height) * 100) . "%");
				$i = 0;
			}
		}

		foreach($chunks as &$chunk){
			$chunk = $chunk->fastSerialize();
		}
		unset($chunk);

		$this->setResult(compact("undoChunks", "chunks"));
	}

	/**
	 * @param Server $server
	 *
	 * @return bool
	 */
	public function onCompletion(Server $server) : bool{
		/** @var Loader $loader */
		$loader = $server->getPluginManager()->getPlugin("BlockSniper");
		if($loader === null){
			return false;
		}
		if(!$loader->isEnabled()){
			return false;
		}
		if(!($player = $server->getPlayer($this->playerName))){
			return false;
		}

		$result = $this->getResult();
		$chunks = $result["chunks"];
		foreach($chunks as &$chunk){
			$chunk = Chunk::fastDeserialize($chunk);
		}
		unset($chunk);
		/** @var Chunk[] $chunks */

		$undoChunks = $result["undoChunks"];
		$level = $player->getLevel();
		if($level instanceof Level){
			foreach($chunks as $hash => $chunk){
				$level->setChunk($chunk->getX(), $chunk->getZ(), $chunk, false);
			}
		}

		foreach($undoChunks as &$undoChunk){
			$undoChunk = Chunk::fastDeserialize($undoChunk);
		}
		unset($undoChunk);

		SessionManager::getPlayerSession($player)->getRevertStore()->saveRevert(new AsyncUndo($chunks, $undoChunks, $this->playerName, $player->getLevel()->getId()));

		return true;
	}
}