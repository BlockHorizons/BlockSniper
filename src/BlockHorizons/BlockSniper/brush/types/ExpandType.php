<?php

namespace BlockHorizons\BlockSniper\brush\types;

use BlockHorizons\BlockSniper\brush\BaseType;
use pocketmine\block\Block;
use pocketmine\item\Item;
use pocketmine\level\Level;
use pocketmine\Player;

class ExpandType extends BaseType {
	
	/*
	 * Expands the terrain with blocks below it.
	 */
	public function __construct(Player $player, Level $level, array $blocks) {
		parent::__construct($player, $level, $blocks);
	}

	/**
	 * @return array
	 */
	public function fillShape(): array {
		$undoBlocks = [];
		$oneHoles = [];
		foreach($this->blocks as $block) {
			if($block->getId() === Item::AIR) {
				$directions = [
					$block->getSide(Block::SIDE_DOWN),
					$block->getSide(Block::SIDE_UP),
					$block->getSide(Block::SIDE_NORTH),
					$block->getSide(Block::SIDE_SOUTH),
					$block->getSide(Block::SIDE_WEST),
					$block->getSide(Block::SIDE_EAST)
				];
				$valid = 0;
				foreach($directions as $direction) {
					if($this->level->getBlock($direction)->getId() !== Item::AIR) {
						$valid++;
					}
				}
				if($valid >= 2) {
					$undoBlocks[] = $block;
				}
				if($valid >= 4) {
					$oneHoles[] = $block;
				}
			}
		}
		foreach($undoBlocks as $selectedBlock) {
			$this->level->setBlock($selectedBlock, ($selectedBlock->getSide(Block::SIDE_DOWN)->getId() === Block::AIR ? $selectedBlock->getSide(Block::SIDE_UP) : $selectedBlock->getSide(Block::SIDE_DOWN)), false, false);
		}
		foreach($oneHoles as $block) {
			$this->level->setBlock($block, ($block->getSide(Block::SIDE_DOWN)->getId() === Block::AIR ? $block->getSide(Block::SIDE_EAST) : $block->getSide(Block::SIDE_DOWN)));
		}

		return array_merge($undoBlocks, $oneHoles);
	}
	
	public function getName(): string {
		return "Expand";
	}
}
