<?php

namespace BlockHorizons\BlockSniper\brush\types;

use BlockHorizons\BlockSniper\brush\BaseType;
use BlockHorizons\BlockSniper\undo\UndoStorer;
use pocketmine\block\Block;
use pocketmine\item\Item;
use pocketmine\level\Level;
use pocketmine\math\Vector3;
use pocketmine\Player;

class DrainType extends BaseType {
	
	/*
	 * Removes all liquid blocks within the brush radius.
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
			$blockId = $block->getId();
			if($blockId === Item::LAVA || $blockId === Item::WATER || $blockId === Item::STILL_LAVA || $blockId === Item::STILL_WATER) {
				$undoBlocks[] = $block;
				$this->level->setBlock(new Vector3($block->x, $block->y, $block->z), Block::get(Block::AIR), false, false);
			}
		}
		$this->getUndoStore()->saveUndo($undoBlocks, $this->player);
		return true;
	}
	
	public function getName(): string {
		return "Drain";
	}
}