<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockSniper\brush\types;

use BlockHorizons\BlockSniper\brush\BaseType;
use pocketmine\block\Block;
use pocketmine\block\Dandelion;
use pocketmine\block\DoublePlant;
use pocketmine\block\Flower;
use pocketmine\block\Leaves;
use pocketmine\block\Leaves2;
use pocketmine\block\TallGrass;

class HeatType extends BaseType {

	const ID = self::TYPE_HEAT;

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
					$this->putBlock($block, Block::WATER);
					break;
				case Block::SNOW:
					$undoBlocks[] = $block;
					$this->putBlock($block, 0);
					break;
				case Block::WATER:
				case Block::FLOWING_WATER:
					if(random_int(0, 4) === 0) {
						$undoBlocks[] = $block;
						$this->putBlock($block, 0);
					}
					break;
				case Block::GRASS:
					$random = random_int(0, 8);
					if($random === 0) {
						$undoBlocks[] = $block;
						$this->putBlock($block, Block::DIRT);
					} elseif($random === 1) {
						$undoBlocks[] = $block;
						$this->putBlock($block, Block::DIRT, 1);
					}
					break;
				case $block instanceof Leaves || $block instanceof Leaves2:
					if(random_int(0, 4) === 0) {
						$undoBlocks[] = $block;
						$this->putBlock($block, 0);
					}
					break;
				case $block instanceof Flower || $block instanceof DoublePlant || $block instanceof TallGrass || $block instanceof Dandelion:
					$undoBlocks[] = $block;
					$this->putBlock($block, Block::TALL_GRASS);
					break;
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
					$this->putBlock($block, Block::WATER);
					break;
				case Block::SNOW:
					$this->putBlock($block, 0);
					break;
				case Block::WATER:
				case Block::FLOWING_WATER:
					if(random_int(0, 4) === 0) {
						$this->putBlock($block, 0);
					}
					break;
				case Block::GRASS:
					$random = random_int(0, 8);
					if($random === 0) {
						$this->putBlock($block, Block::DIRT);
					} elseif($random === 1) {
						$this->putBlock($block, Block::DIRT, 1);
					}
					break;
				case $block instanceof Leaves || $block instanceof Leaves2:
					if(random_int(0, 4) === 0) {
						$this->putBlock($block, 0);
					}
					break;
				case $block instanceof Flower || $block instanceof DoublePlant || $block instanceof TallGrass || $block instanceof Dandelion:
					$this->putBlock($block, Block::TALL_GRASS);
					break;
			}
		}
	}

	public function getName(): string {
		return "Heat";
	}
}