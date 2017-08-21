<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockSniper\brush\async\tasks;

use BlockHorizons\BlockSniper\brush\BaseType;
use BlockHorizons\BlockSniper\Loader;
use BlockHorizons\BlockSniper\sessions\SessionManager;
use BlockHorizons\BlockSniper\undo\async\AsyncUndo;
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
		$undoChunks = clone $chunks;
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
			$vector3 = $center->add($block->x - floor($schematic->getWidth() / 2), $block->y, $block->z - floor($schematic->getLength() / 2));
			$index = Level::chunkHash($vector3->x >> 4, $vector3->z >> 4);
			if(isset($chunks[$index])) {
				$manager->setBlockIdAt((int) $vector3->x, (int) $vector3->y, (int) $vector3->z, $block->getId());
				$manager->setBlockDataAt((int) $vector3->x, (int) $vector3->y, (int) $vector3->z, $block->getDamage());

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
			"undoChunks" => $undoChunks,
			"chunks" => $serializedChunks
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
		$chunks = $result["chunks"];
		foreach($chunks as &$chunk) {
			$chunk = Chunk::fastDeserialize($chunk);
		}
		unset($chunk);
		$undoChunks = $result["undoChunks"];
		$level = $player->getLevel();
		if($level instanceof Level) {
			foreach($chunks as $hash => $chunk) {
				$x = $z = 0;
				Level::getXZ($hash, $x, $z);
				$level->setChunk($x, $z, $chunk);
			}
		}
		SessionManager::getPlayerSession($player)->getRevertStorer()->saveRevert(new AsyncUndo($undoChunks, $this->playerName, $player->getLevel()->getId()));
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