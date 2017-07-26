<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockSniper\brush\async\tasks;

use BlockHorizons\BlockSniper\brush\BaseShape;
use BlockHorizons\BlockSniper\brush\BrushManager;
use BlockHorizons\BlockSniper\cloning\BaseClone;
use BlockHorizons\BlockSniper\cloning\types\CopyType;
use BlockHorizons\BlockSniper\cloning\types\TemplateType;
use BlockHorizons\BlockSniper\Loader;
use pocketmine\block\Block;
use pocketmine\level\format\Chunk;
use pocketmine\level\Level;
use pocketmine\Server;
use libschematic\Schematic;

class CloneTask extends AsyncBlockSniperTask {

	/** @var int */
	protected $type = self::TYPE_COPY;
	/** @var BaseShape */
	protected $shape = null;
	/** @var string */
	protected $chunks = "";
	/** @var int */
	protected $cloneType = 0;
	/** @var bool */
	protected $saveAir = false;
	/** @var string */
	protected $name = "";

	public function __construct(BaseShape $shape, array $chunks, int $cloneType = 0, string $name = "", bool $saveAir = false) {
		$this->shape = $shape;
		$this->chunks = serialize($chunks);
		$this->cloneType = $cloneType;
		$this->name = $name;
		$this->saveAir = $saveAir;
	}

	public function onRun() {
		$chunks = unserialize($this->chunks);
		$processedBlocks = 0;
		$shape = $this->shape;
		foreach($chunks as $hash => $data) {
			$chunks[$hash] = Chunk::fastDeserialize($data);
		}
		/** @var Chunk[] $chunks */
		$vectorsInside = $shape->getBlocksInside(true);
		$blocks = [];
		$i = 0;
		foreach($vectorsInside as $vector3) {
			$index = Level::chunkHash($vector3->x >> 4, $vector3->z >> 4);
			if(isset($chunks[$index])) {
				$pos = [$vector3->x & 0x0f, $vector3->y, $vector3->z & 0x0f];
				$block = Block::get($chunks[$index]->getBlockId($pos[0], $pos[1], $pos[2]), $chunks[$index]->getBlockData($pos[0], $pos[1], $pos[2]));
				$block->setComponents($vector3->x, $vector3->y, $vector3->z);
				$blocks[] = $block;
				$processedBlocks++;
			}
			if(++$i === (int) ($shape->getApproximateProcessedBlocks() / 100)) {
				$this->publishProgress(round($processedBlocks / $shape->getApproximateProcessedBlocks() * 100) . "%");
				$i = 0;
			}
		}
		$this->setResult([
			"blocks" => $blocks,
		]);
	}

	/**
	 * @param Server $server
	 *
	 * @return bool
	 */
	public function onCompletion(Server $server): bool {
		$shape = $this->shape;
		if(($player = $shape->getPlayer($server)) === null) {
			return false;
		}
		/** @var Loader $loader */
		$loader = $server->getPluginManager()->getPlugin("BlockSniper");
		if($loader === null) {
			return false;
		}
		if(!$loader->isEnabled()) {
			return false;
		}
		/** @var Block[] $blocks */
		$blocks = $this->getResult()["blocks"];
		$level = $server->getLevel($shape->getLevelId());
		if($level instanceof Level) {
			foreach($blocks as &$block) {
				$block->setLevel($shape->getLevel());
			}
		}
		switch($this->cloneType) {
			case BaseClone::TYPE_COPY:
				$type = new CopyType($loader->getCloneStorer(), $player, $this->saveAir, $shape->getCenter(), $blocks);
				$type->saveClone();
				break;
			case BaseClone::TYPE_TEMPLATE:
				$type = new TemplateType($loader->getCloneStorer(), $player, $this->saveAir, $shape->getCenter(), $blocks, $this->name);
				$type->saveClone();
				break;
			case BaseClone::TYPE_SCHEMATIC:
				$schematic = new Schematic();
				$size = BrushManager::get($player)->getSize();
				$schematic
					->setBlocks($shape->getBlocksInside())
					->setMaterials(Schematic::MATERIALS_ALPHA)
					->encode()
					->setLength($size * 2 + 1)
					->setHeight($size * 2 + 1)
					->setWidth($size * 2 + 1)
					->save($loader->getDataFolder() . "schematics/" . $this->name . ".schematic");
				break;
		}
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