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

/*
 * Flattens the terrain below the selected point and removes the blocks above it within the brush radius.
 */
class FlattenAllType extends BaseType {

	const ID = self::TYPE_FLATTEN_ALL;

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
			if($block->y <= $this->center->y && ($block->getId() === Item::AIR || $block instanceof Flowable)) {
				$undoBlocks[] = $block;
				$this->putBlock($block, $randomBlock->getId(), $randomBlock->getDamage());
			}
			if($block->y > $this->center->y && $block->getId() !== Item::AIR) {
				$undoBlocks[] = $block;
				$this->putBlock($block, 0);
			}
		}
		return $undoBlocks;
	}

	public function fillAsynchronously(): void {
		foreach($this->blocks as $block) {
			$randomBlock = $this->brushBlocks[array_rand($this->brushBlocks)];
			if($block->y <= $this->center->y && ($block->getId() === Item::AIR || $block instanceof Flowable)) {
				$this->putBlock($block, $randomBlock->getId(), $randomBlock->getDamage());
			}
			if($block->y > $this->center->y && $block->getId() !== Item::AIR) {
				$this->putBlock($block, 0);
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
