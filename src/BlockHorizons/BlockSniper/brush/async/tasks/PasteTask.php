<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockSniper\brush\async\tasks;

use BlockHorizons\BlockSniper\brush\BaseType;
use BlockHorizons\BlockSniper\Loader;
use BlockHorizons\BlockSniper\undo\Undo;
use libschematic\Schematic;
use pocketmine\block\Block;
use pocketmine\level\format\Chunk;
use pocketmine\level\Level;
use pocketmine\math\Vector3;
use pocketmine\Server;

class PasteTask extends AsyncBlockSniperTask {

	/** @var int */
	protected $taskType = self::TYPE_PASTE;
	/** @var string */
	private $file = "";
	/** @var Vector3 */
	private $center = null;
	/** @var string[] */
	private $chunks = "";
	/** @var string */
	private $playerName = "";

	public function __construct(string $file, Vector3 $center, array $chunks, string $playerName) {
		$this->file = $file;
		$this->center = $center;
		$this->chunks = serialize($chunks);
		$this->playerName = $playerName;
	}

	public function onRun() {
		$chunks = unserialize($this->chunks);
		$file = $this->file;
		$center = $this->center;
		$schematic = new Schematic($file);
		$schematic->decode();
		$schematic->fixBlockIds();

		$processedBlocks = 0;
		foreach($chunks as $hash => $data) {
			$chunks[$hash] = Chunk::fastDeserialize($data);
		}
		/** @var Chunk[] $chunks */
		/** @var Block[] $blocksInside */
		$blocksInside = $schematic->getBlocks();
		$undoBlocks = [];
		$manager = BaseType::establishChunkManager($chunks);
		$i = 0;
		foreach($blocksInside as $block) {
			$vector3 = $block->add($center);
			$index = Level::chunkHash($vector3->x >> 4, $vector3->z >> 4);
			if(isset($chunks[$index])) {
				$undoBlock = Block::get($manager->getBlockIdAt($vector3->x, $vector3->y, $vector3->z), $manager->getBlockDataAt($vector3->x, $vector3->y, $vector3->z));
				$undoBlock->setComponents($vector3->x, $vector3->y, $vector3->z);
				$undoBlocks[] = $undoBlock;

				$manager->setBlockIdAt($vector3->x, $vector3->y, $vector3->z, $block->getId());
				$manager->setBlockDataAt($vector3->x, $vector3->y, $vector3->z, $block->getDamage());

				$processedBlocks++;
			}
			if(++$i === (int) ($schematic->getLength() * $schematic->getWidth() * $schematic->getHeight() / 100)) {
				$this->publishProgress(ceil($processedBlocks / ($schematic->getLength() * $schematic->getWidth() * $schematic->getHeight()) * 100) . "%");
				$i = 0;
			}
		}
		$serializedChunks = $chunks;
		foreach($serializedChunks as &$chunk) {
			$chunk = $chunk->fastSerialize();
		}
		unset($chunk);

		$this->setResult([
			"undoBlocks" => serialize($undoBlocks),
			"chunks" => serialize($serializedChunks)
		]);
	}

	/**
	 * @param Server $server
	 *
	 * @return bool
	 */
	public function onCompletion(Server $server): bool {
		/** @var Loader $loader */
		$loader = $server->getPluginManager()->getPlugin("BlockSniper");
		if($loader === null) {
			return false;
		}
		if(!$loader->isEnabled()) {
			return false;
		}
		if(!($player = $server->getPlayer($this->playerName))) {
			return false;
		}
		$result = $this->getResult();
		$chunks = unserialize($result["chunks"]);
		foreach($chunks as &$chunk) {
			$chunk = Chunk::fastDeserialize($chunk);
		}
		unset($chunk);
		$undoBlocks = unserialize($result["undoBlocks"]);
		$level = $player->getLevel();
		if($level instanceof Level) {
			foreach($chunks as $hash => $chunk) {
				$x = $z = 0;
				Level::getXZ($hash, $x, $z);
				$level->setChunk($x, $z, $chunk);
			}
		}
		$loader->getRevertStorer()->saveRevert((new Undo($undoBlocks))->setPlayerName($player->getName())->setTouchedChunks($chunks)->setAsynchronous(), $player);
		return true;
	}

	/**
	 * @param Server $server
	 * @param mixed  $progress
	 *
	 * @return bool
	 */
	public function onProgressUpdate(Server $server, $progress): bool {
		$loader = $server->getPluginManager()->getPlugin("BlockSniper");
		if($loader instanceof Loader) {
			if($loader->isEnabled()) {
				$loader->getLogger()->debug($progress);
				return true;
			}
		}
		$this->setGarbage();
		return false;
	}
}