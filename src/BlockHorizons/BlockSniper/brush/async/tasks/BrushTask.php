<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\brush\async\tasks;

use BlockHorizons\BlockSniper\brush\BaseShape;
use BlockHorizons\BlockSniper\brush\BaseType;
use BlockHorizons\BlockSniper\Loader;
use BlockHorizons\BlockSniper\revert\async\AsyncUndo;
use BlockHorizons\BlockSniper\sessions\SessionManager;
use pocketmine\block\Block;
use pocketmine\level\format\Chunk;
use pocketmine\level\Level;
use pocketmine\math\Vector2;
use pocketmine\Server;

class BrushTask extends AsyncBlockSniperTask{

	/** @var BaseShape */
	private $shape = null;
	/** @var string */
	private $chunks = "";
	/** @var BaseType */
	private $type = null;
	/** @var string */
	private $plotPoints = "";

	public function __construct(BaseShape $shape, BaseType $type, array $chunks, array $plotPoints){
		$this->shape = $shape;
		$this->type = $type;
		$this->chunks = serialize($chunks);
		$this->plotPoints = serialize($plotPoints);
	}

	public function onRun() : void{
		$chunks = unserialize($this->chunks, ["allowed_classes" => [Chunk::class]]);
		$type = $this->type;
		$shape = $this->shape;

		$undoChunks = $chunks;

		foreach($chunks as $hash => $data){
			$chunks[$hash] = Chunk::fastDeserialize($data);
		}
		/** @var Chunk[] $chunks */

		$vectorsInside = $shape->getBlocksInside(true);
		$manager = BaseType::establishChunkManager($chunks);

		$type->setBlocksInside($this->blocks($vectorsInside, $shape, $chunks))->setAsynchronous()->setChunkManager($manager)->fillShape(unserialize($this->plotPoints, ["allowed_classes" => [Vector2::class]]));

		$this->setResult(compact("undoChunks", "chunks"));
	}

	private function blocks(\Generator $vectorsInside, BaseShape $shape, array $chunks) : \Generator{
		$i = $processedBlocks = 0;
		$percentageBlocks = $shape->getApproximateProcessedBlocks();
		foreach($vectorsInside as $vector3){
			$index = Level::chunkHash($vector3->x >> 4, $vector3->z >> 4);

			$pos = [$vector3->x & 0x0f, $vector3->y, $vector3->z & 0x0f];
			$block = Block::get($chunks[$index]->getBlockId($pos[0], $pos[1], $pos[2]), $chunks[$index]->getBlockData($pos[0], $pos[1], $pos[2]));
			$block->setComponents($vector3->x, $vector3->y, $vector3->z);
			yield $block;

			++$processedBlocks;
			if(++$i === $percentageBlocks){ // This is messed up with hollow shapes. Got to find a fix for that.
				if($this->isAborted()){
					return;
				}
				$this->publishProgress(ceil($processedBlocks / $shape->getApproximateProcessedBlocks() * 100) . "%");
				$i = 0;
			}
		}
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
		if(!($player = $this->shape->getPlayer($server))){
			return false;
		}

		$result = $this->getResult();
		/** @var Chunk[] $chunks */
		$chunks = $result["chunks"];
		$undoChunks = $result["undoChunks"];
		$level = $server->getLevel($this->shape->getLevelId());

		foreach($undoChunks as &$undoChunk){
			$undoChunk = Chunk::fastDeserialize($undoChunk);
		}
		unset($undoChunk);

		if($level instanceof Level){
			foreach($chunks as $hash => $chunk){
				$level->setChunk($chunk->getX(), $chunk->getZ(), $chunk, false);
			}
		}

		SessionManager::getPlayerSession($player)->getRevertStore()->saveRevert(new AsyncUndo($chunks, $undoChunks, $player->getName(), $player->getLevel()->getId()));

		return true;
	}
}