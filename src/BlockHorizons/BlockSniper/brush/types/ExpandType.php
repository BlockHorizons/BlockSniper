<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockSniper\brush\types;

use BlockHorizons\BlockSniper\brush\BaseType;
use pocketmine\block\Block;
use pocketmine\item\Item;
use pocketmine\level\ChunkManager;
use pocketmine\Player;

class ExpandType extends BaseType {

	/*
	 * Expands the terrain with blocks below it.
	 */
	public function __construct(Player $player, ChunkManager $level, array $blocks) {
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
					if($direction->getId() !== Item::AIR) {
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
			if($this->isAsynchronous()) {
				$bottom = $this->getChunkManager()->getSide($selectedBlock->x, $selectedBlock->y, $selectedBlock->z, Block::SIDE_DOWN);
				$top = $this->getChunkManager()->getSide($selectedBlock->x, $selectedBlock->y, $selectedBlock->z, Block::SIDE_UP);
				$this->getChunkManager()->setBlockIdAt($selectedBlock->x, $selectedBlock->y, $selectedBlock->z, $bottom->getId() === Block::AIR ? $top->getId() : $bottom->getId());
				$this->getChunkManager()->setBlockIdAt($selectedBlock->x, $selectedBlock->y, $selectedBlock->z, $bottom->getId() === Block::AIR ? $top->getDamage() : $bottom->getDamage());
			} else {
				$this->getLevel()->setBlock($selectedBlock, ($selectedBlock->getSide(Block::SIDE_DOWN)->getId() === Block::AIR ? $selectedBlock->getSide(Block::SIDE_UP) : $selectedBlock->getSide(Block::SIDE_DOWN)), false, false);
			}
		}
		foreach($oneHoles as $block) {
			if($this->isAsynchronous()) {
				$bottom = $this->getChunkManager()->getSide($block->x, $block->y, $block->z, Block::SIDE_DOWN);
				$east = $this->getChunkManager()->getSide($block->x, $block->y, $block->z, Block::SIDE_UP);
				$this->getChunkManager()->setBlockIdAt($block->x, $block->y, $block->z, $bottom->getId() === Block::AIR ? $east->getId() : $bottom->getId());
				$this->getChunkManager()->setBlockIdAt($block->x, $block->y, $block->z, $bottom->getId() === Block::AIR ? $east->getDamage() : $bottom->getDamage());
			} else {
				$this->getLevel()->setBlock($block, ($block->getSide(Block::SIDE_DOWN)->getId() === Block::AIR ? $block->getSide(Block::SIDE_EAST) : $block->getSide(Block::SIDE_DOWN)));
			}
		}

		return array_merge($undoBlocks, $oneHoles);
	}

	public function getName(): string {
		return "Expand";
	}
}
