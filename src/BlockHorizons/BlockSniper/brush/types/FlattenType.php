<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockSniper\brush\types;

use BlockHorizons\BlockSniper\brush\BaseType;
use pocketmine\block\Flowable;
use pocketmine\item\Item;
use pocketmine\level\ChunkManager;
use pocketmine\level\Position;
use pocketmine\Player;

class FlattenType extends BaseType {

	/*
	 * Flattens the terrain below the selected point within the brush radius.
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
			if(($block->getId() === Item::AIR || $block instanceof Flowable) && $block->y <= $this->center->y) {
				if($block->getId() !== $randomBlock->getId()) {
					$undoBlocks[] = $block;
				}
				if($this->isAsynchronous()) {
					$this->getChunkManager()->setBlockIdAt($block->x, $block->y, $block->z, $randomBlock->getId());
					$this->getChunkManager()->setBlockDataAt($block->x, $block->y, $block->z, $randomBlock->getDamage());
				} else {
					$this->getLevel()->setBlock($block, $randomBlock, false, false);
				}
			}
		}
		return $undoBlocks;
	}

	public function getName(): string {
		return "Flatten";
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
