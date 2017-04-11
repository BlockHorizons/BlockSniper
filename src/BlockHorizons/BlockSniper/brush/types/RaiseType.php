<?php

namespace BlockHorizons\BlockSniper\brush\types;

use BlockHorizons\BlockSniper\brush\BaseType;
use BlockHorizons\BlockSniper\undo\UndoStorer;
use pocketmine\block\Block;
use pocketmine\level\Level;
use pocketmine\math\Vector3;
use pocketmine\Player;

class RaiseType extends BaseType {
	
	/*
	 * Raises the terrain by one block within the brush radius.
	 */
	public function __construct(UndoStorer $undoStorer, Player $player, Level $level, array $blocks) {
		parent::__construct($undoStorer, $player, $level, $blocks);
	}
	
	/**
	 * @return bool
	 */
	public function fillShape(): bool {
		$savedBlocks = [];
		$holeBlocks = [];
		$undoBlocks = [];
		$peakBlocks = [];
		$sides = [
			Block::SIDE_NORTH,
			Block::SIDE_EAST,
			Block::SIDE_SOUTH,
			Block::SIDE_WEST
		];
		foreach($this->blocks as $block) {
			$valid = 0;
			foreach($sides as $side) {
				if($block->getSide($side)->getId() !== Block::AIR) {
					$valid++;
				}
			}
			if($valid >= 3) {
				$holeBlocks[] = $block;
			}
			if($valid === 0) {
				$peakBlocks[] = $block;
			}
		}
		foreach($holeBlocks as $selectedBlock) {
			$undoBlocks[] = $selectedBlock;
			$this->level->setBlock($selectedBlock, $this->level->getBlock(new Vector3($selectedBlock->x, $selectedBlock->y - 1, $selectedBlock->z)), false, false);
		}
		foreach($peakBlocks as $selectedBlock) {
			$undoBlocks[] = $selectedBlock;
			$this->level->setBlock($selectedBlock, Block::get(Block::AIR), false, false);
		}
		foreach($this->blocks as $block) {
			if($block->getSide(Block::SIDE_UP)->getId() === Block::AIR && $block->getId() !== Block::AIR) {
				$savedBlocks[] = $block;
			}
		}
		foreach($savedBlocks as $selectedBlock) {
			$undoBlocks[] = $selectedBlock->getSide(Block::SIDE_UP);
			$this->level->setBlock($selectedBlock->getSide(Block::SIDE_UP), $selectedBlock, false, false);
		}
		$this->getUndoStore()->saveUndo($undoBlocks, $this->player);
		return true;
	}
	
	public function getName(): string {
		return "Raise";
	}
}
