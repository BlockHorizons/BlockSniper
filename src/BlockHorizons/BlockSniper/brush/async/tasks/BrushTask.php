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
use pocketmine\scheduler\AsyncTask;
use pocketmine\Server;
use pocketmine\utils\TextFormat;

class BrushTask extends AsyncTask{

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
		$type = $this->type;
		$shape = $this->shape;

		// Publish progress immediately so that there will be no delay until the progress indicator appears.
		$this->publishProgress([$shape->getPlayerName(), 0]);

		$chunks = unserialize($this->chunks, ["allowed_classes" => [Chunk::class]]);

		$undoChunks = $chunks;

		foreach($chunks as $hash => $data){
			$chunks[$hash] = Chunk::fastDeserialize($data);
		}
		/** @var Chunk[] $chunks */
		$manager = BaseType::establishChunkManager($chunks);

		$type->setBlocksInside($this->blocks($shape, $chunks))->setAsynchronous()->setChunkManager($manager)->fillShape(unserialize($this->plotPoints, ["allowed_classes" => [Vector2::class]]));

		$this->setResult(compact("undoChunks", "chunks"));
	}

	private function blocks(BaseShape $shape, array $chunks) : \Generator{
		$blockCount = $shape->getBlockCount();
		$blocksPerPercentage = (int) round($blockCount / 100);
		$percentageBlocks = $blocksPerPercentage;

		$i = 0;
		foreach($shape->getBlocksInside(true) as $vector3){
			$index = Level::chunkHash($vector3->x >> 4, $vector3->z >> 4);
			if(!isset($chunks[$index])){
				continue;
			}

			[$posX, $posY, $posZ] = [(int) $vector3->x & 0x0f, (int) $vector3->y, (int) $vector3->z & 0x0f];
			$block = Block::get($chunks[$index]->getBlockId($posX, $posY, $posZ), $chunks[$index]->getBlockData($posX, $posY, $posZ));
			$block->setComponents($vector3->x, $vector3->y, $vector3->z);

			$i++;
			if($i === $percentageBlocks){
				$this->publishProgress([$shape->getPlayerName(), (int) ceil($i / $blockCount * 20)]);
				$percentageBlocks += $blocksPerPercentage;
			}
			yield $block;
		}
	}

	public function onProgressUpdate(Server $server, $progress) : void{
		[$playerName, $progress] = $progress;
		if(($player = $server->getPlayer($playerName)) === null){
			return;
		}
		$player->sendPopup(TextFormat::GREEN . str_repeat("|", $progress) . TextFormat::RED . str_repeat("|", 20 - $progress));
	}

	public function onCompletion(Server $server) : void{
		/** @var Loader $loader */
		$loader = $server->getPluginManager()->getPlugin("BlockSniper");
		if(!$loader->isEnabled()){
			return;
		}
		if(!($player = $this->shape->getPlayer($server))){
			return;
		}

		$result = $this->getResult();
		/** @var Chunk[] $chunks */
		$chunks = $result["chunks"];
		$undoChunks = $result["undoChunks"];
		$level = $this->shape->getLevel();

		foreach($undoChunks as &$undoChunk){
			$undoChunk = Chunk::fastDeserialize($undoChunk);
		}
		unset($undoChunk);

		foreach($chunks as $hash => $chunk){
			$level->setChunk($chunk->getX(), $chunk->getZ(), $chunk, false);
		}

		SessionManager::getPlayerSession($player)->getRevertStore()->saveRevert(new AsyncUndo($chunks, $undoChunks, $player->getName(), $player->getLevel()->getId()));
	}
}