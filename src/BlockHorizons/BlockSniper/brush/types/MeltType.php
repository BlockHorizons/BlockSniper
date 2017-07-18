<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockSniper\brush\types;

use BlockHorizons\BlockSniper\brush\BaseType;
use pocketmine\block\Block;
use pocketmine\item\Item;
use pocketmine\level\ChunkManager;
use pocketmine\level\Level;
use pocketmine\Player;

class MeltType extends BaseType {
	
	/*
	 * Melts away every block with more than 2 open sides within the brush radius.
	 */
	public function __construct(Player $player, ChunkManager $level, array $blocks) {
		parent::__construct($player, $level, $blocks);
	}

	/**
	 * @return array
	 */
	public function fillShape(): array {
		$undoBlocks = [];
		foreach($this->blocks as $block) {
			if($block->getId() !== Item::AIR) {
				if($this->isAsynchronous()) {
					$directions = [
						$this->getChunkManager()->getSide($block->x, $block->y, $block->z, Block::SIDE_DOWN),
						$this->getChunkManager()->getSide($block->x, $block->y, $block->z, Block::SIDE_UP),
						$this->getChunkManager()->getSide($block->x, $block->y, $block->z, Block::SIDE_NORTH),
						$this->getChunkManager()->getSide($block->x, $block->y, $block->z, Block::SIDE_SOUTH),
						$this->getChunkManager()->getSide($block->x, $block->y, $block->z, Block::SIDE_WEST),
						$this->getChunkManager()->getSide($block->x, $block->y, $block->z, Block::SIDE_EAST),
					];
				} else {
					$directions = [
						$block->getSide(Block::SIDE_DOWN),
						$block->getSide(Block::SIDE_UP),
						$block->getSide(Block::SIDE_NORTH),
						$block->getSide(Block::SIDE_SOUTH),
						$block->getSide(Block::SIDE_WEST),
						$block->getSide(Block::SIDE_EAST)
					];
				}
				
				$valid = 0;
				foreach($directions as $direction) {
					if($direction->getId() === Item::AIR) {
						$valid++;
					}
				}
				if($valid >= 2) {
					$undoBlocks[] = $block;
				}
			}
		}
		foreach($undoBlocks as $selectedBlock) {
			if($this->isAsynchronous()) {
				$this->getChunkManager()->setBlockIdAt($selectedBlock->x, $selectedBlock->y, $selectedBlock->z, Block::AIR);
			} else {
				$this->getLevel()->setBlock($selectedBlock, Block::get(Block::AIR), false, false);
			}
		}
		return $undoBlocks;
	}
	
	public function getName(): string {
		return "Melt";
	}
}