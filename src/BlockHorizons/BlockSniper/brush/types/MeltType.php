<?php

namespace BlockHorizons\BlockSniper\brush\types;

use BlockHorizons\BlockSniper\brush\BaseType;
use BlockHorizons\BlockSniper\undo\UndoStorer;
use pocketmine\block\Block;
use pocketmine\item\Item;
use pocketmine\level\Level;
use pocketmine\Player;

class MeltType extends BaseType {
	
	/*
	 * Melts away every block with more than 2 open sides within the brush radius.
	 */
	public function __construct(UndoStorer $undoStorer, Player $player, Level $level, array $blocks) {
		parent::__construct($undoStorer, $player, $level, $blocks);
	}
	
	/**
	 * @return bool
	 */
	public function fillShape(): bool {
		$undoBlocks = [];
		foreach($this->blocks as $block) {
			if($block->getId() !== Item::AIR) {
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
					if($this->level->getBlock($direction)->getId() === Item::AIR) {
						$valid++;
					}
				}
				if($valid >= 2) {
					$undoBlocks[] = $block;
				}
			}
		}
		foreach($undoBlocks as $selectedBlock) {
			$this->level->setBlock($selectedBlock, Block::get(Block::AIR), false, false);
		}
		$this->getUndoStore()->saveUndo($undoBlocks, $this->player);
		return true;
	}
	
	public function getName(): string {
		return "Melt";
	}
}