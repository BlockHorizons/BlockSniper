<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockSniper\brush\types;

use BlockHorizons\BlockSniper\brush\BaseType;
use pocketmine\block\Block;

/*
 * Removes all non-natural blocks within the brush radius.
 */
class CleanType extends BaseType {

	const ID = self::TYPE_CLEAN;

	public function getName(): string {
		return "Clean";
	}

	/**
	 * @return Block[]
	 */
	protected function fillSynchronously(): array {
		$undoBlocks = [];
		foreach($this->blocks as $block) {
			$blockId = $block->getId();
			if($blockId !== Block::AIR && $blockId !== Block::STONE && $blockId !== Block::GRASS && $blockId !== Block::DIRT && $blockId !== Block::GRAVEL && $blockId !== Block::SAND && $blockId !== Block::SANDSTONE) {
				$undoBlocks[] = $block;
				$this->putBlock($block, 0);
			}
		}
		return $undoBlocks;
	}

	protected function fillAsynchronously(): void {
		foreach($this->blocks as $block) {
			$blockId = $block->getId();
			if($blockId !== Block::AIR && $blockId !== Block::STONE && $blockId !== Block::GRASS && $blockId !== Block::DIRT && $blockId !== Block::GRAVEL && $blockId !== Block::SAND && $blockId !== Block::SANDSTONE) {
				$this->putBlock($block, $blockId);
			}
		}
	}
}
