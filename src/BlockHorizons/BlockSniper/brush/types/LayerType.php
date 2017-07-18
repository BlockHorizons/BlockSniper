<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockSniper\brush\types;

use BlockHorizons\BlockSniper\brush\BaseType;
use BlockHorizons\BlockSniper\brush\BrushManager;
use pocketmine\level\ChunkManager;
use pocketmine\level\format\Chunk;
use pocketmine\level\Level;
use pocketmine\level\Position;
use pocketmine\math\Vector3;
use pocketmine\Player;

class LayerType extends BaseType {
	
	/*
	 * Lays a thin layer of blocks within the brush radius.
	 */
	public function __construct(Player $player, ChunkManager $level, array $blocks) {
		parent::__construct($player, $level, $blocks);
		$this->center = $player->getTargetBlock(100);
	}

	/**
	 * @return array
	 */
	public function fillShape(): array {
		$undoBlocks = [];
		foreach($this->blocks as $block) {
			$randomBlock = $this->brushBlocks[array_rand($this->brushBlocks)];
			if($block->getId() !== $randomBlock->getId()) {
				$undoBlocks[] = $block;
			}
			if($this->isAsynchronous()) {
				$this->getChunkManager()->setBlockIdAt($block->x, $this->center->y + 1, $block->z, $randomBlock->getId());
				$this->getChunkManager()->setBlockDataAt($block->x, $this->center->y + 1, $block->z, $randomBlock->getDamage());
			} else {
				$this->getLevel()->setBlock(new Vector3($block->x, $this->center->y + 1, $block->z), $randomBlock, false, false);
			}
		}
		return $undoBlocks;
	}
	
	public function getName(): string {
		return "Layer";
	}

	/**
	 * Returns the center of this type.
	 *
	 * @return Position
	 */
	public function getCenter(): Position {
		return $this->center;
	}
}

