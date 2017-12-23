<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockSniper\brush\types;

use BlockHorizons\BlockSniper\brush\BaseType;
use pocketmine\block\Block;

class WarmType extends BaseType {

	const ID = self::TYPE_WARM;

	public function fillSynchronously(): array {
		$undoBlocks = [];
		foreach($this->blocks as $block) {
			switch($block->getId()) {
				case Block::ICE:
					$undoBlocks[] = $block;
					$this->putBlock($block, Block::WATER);
					break;
				case Block::SNOW_LAYER:
					$undoBlocks[] = $block;
					$this->putBlock($block, 0);
					break;
				case Block::PACKED_ICE:
					$undoBlocks[] = $block;
					$this->putBlock($block, Block::ICE);
			}
		}
		return $undoBlocks;
	}

	public function fillAsynchronously(): void {
		foreach($this->blocks as $block) {
			switch($block->getId()) {
				case Block::ICE:
					$this->putBlock($block, Block::WATER);
					break;
				case Block::SNOW_LAYER:
					$this->putBlock($block, 0);
					break;
				case Block::PACKED_ICE:
					$this->putBlock($block, Block::ICE);
			}
		}
	}

	public function getName(): string {
		return "Warm";
	}
}