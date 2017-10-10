<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockSniper\brush\types;

use BlockHorizons\BlockSniper\brush\BaseType;
use pocketmine\block\Block;
use pocketmine\block\Flowable;
use pocketmine\item\Item;
use pocketmine\level\ChunkManager;
use pocketmine\math\Vector3;
use pocketmine\Player;

class FlattenallType extends BaseType {

	/** @var int */
	protected $id = self::TYPE_FLATTENALL;

	/*
	 * Flattens the terrain below the selected point and removes the blocks above it within the brush radius.
	 */
	public function __construct(Player $player, ChunkManager $level, array $blocks) {
		parent::__construct($player, $level, $blocks);
		$this->center = $player->getTargetBlock(100)->asVector3();
	}

	/**
	 * @return Block[]
	 */
	public function fillSynchronously(): array {
		$undoBlocks = [];
		foreach($this->blocks as $block) {
			$randomBlock = $this->brushBlocks[array_rand($this->brushBlocks)];
			if(($block->getId() === Item::AIR || $block instanceof Flowable) && $block->y <= $this->center->y) {
				if($block->getId() !== $randomBlock->getId()) {
					$undoBlocks[] = $block;
				}
				$this->getLevel()->setBlock($block, $randomBlock, false, false);
			}
			if($block->getId() !== Item::AIR && $block->y > $this->center->y) {
				$undoBlocks[] = $block;
				$this->getLevel()->setBlock($block, Block::get(Block::AIR));
			}
		}
		return $undoBlocks;
	}

	public function fillAsynchronously(): void {
		foreach($this->blocks as $block) {
			$randomBlock = $this->brushBlocks[array_rand($this->brushBlocks)];
			if(($block->getId() === Item::AIR || $block instanceof Flowable) && $block->y <= $this->center->y) {
				$this->getChunkManager()->setBlockIdAt($block->x, $block->y, $block->z, $randomBlock->getId());
				$this->getChunkManager()->setBlockDataAt($block->x, $block->y, $block->z, $randomBlock->getDamage());
			}
			if($block->getId() !== Item::AIR && $block->y > $this->center->y) {
				$undoBlocks[] = $block;
				$this->getChunkManager()->setBlockIdAt($block->x, $block->y, $block->z, Block::AIR);
			}
		}
	}

	public function getName(): string {
		return "Flatten All";
	}

	/**
	 * Returns the center of this type.
	 *
	 * @return Vector3
	 */
	public function getCenter(): Vector3 {
		return $this->center;
	}
}
