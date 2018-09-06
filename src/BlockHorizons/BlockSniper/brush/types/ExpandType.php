<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\brush\types;

use BlockHorizons\BlockSniper\brush\BaseType;
use pocketmine\block\Block;
use pocketmine\item\Item;

/*
 * Expands the terrain with blocks below it.
 */

class ExpandType extends BaseType{

	const ID = self::TYPE_EXPAND;

	/**
	 * @return \Generator
	 */
	public function fillSynchronously() : \Generator{
		$undoBlocks = [];
		$oneHoles = [];
		foreach($this->blocks as $block){
			if($block->getId() === Item::AIR){
				$directions = [
					$block->getSide(Block::SIDE_DOWN),
					$block->getSide(Block::SIDE_UP),
					$block->getSide(Block::SIDE_NORTH),
					$block->getSide(Block::SIDE_SOUTH),
					$block->getSide(Block::SIDE_WEST),
					$block->getSide(Block::SIDE_EAST)
				];

				$valid = 0;
				foreach($directions as $direction){
					if($direction->getId() !== Item::AIR){
						$valid++;
					}
				}
				if($valid >= 2){
					$undoBlocks[] = $block;
				}
				if($valid >= 4){
					$oneHoles[] = $block;
				}
			}
		}
		foreach($undoBlocks as $selectedBlock){
			/** @var Block $undoBlock */
			$undoBlock = ($selectedBlock->getSide(Block::SIDE_DOWN)->getId() === Block::AIR ? $selectedBlock->getSide(Block::SIDE_UP) : $selectedBlock->getSide(Block::SIDE_DOWN));
			yield $undoBlock;
			$this->putBlock($selectedBlock, $undoBlock->getId(), $undoBlock->getDamage());
		}
		foreach($oneHoles as $block){
			/** @var Block $oneHole */
			$oneHole = ($block->getSide(Block::SIDE_DOWN)->getId() === Block::AIR ? $block->getSide(Block::SIDE_EAST) : $block->getSide(Block::SIDE_DOWN));
			yield $oneHole;
			$this->putBlock($block, $oneHole->getId(), $oneHole->getDamage());
		}
	}

	public function fillAsynchronously() : void{
		$oneHoles = [];
		$blocks = [];
		foreach($this->blocks as $block){
			if($block->getId() === Item::AIR){
				$directions = [
					$this->getChunkManager()->getSide($block->x, $block->y, $block->z, Block::SIDE_DOWN),
					$this->getChunkManager()->getSide($block->x, $block->y, $block->z, Block::SIDE_UP),
					$this->getChunkManager()->getSide($block->x, $block->y, $block->z, Block::SIDE_NORTH),
					$this->getChunkManager()->getSide($block->x, $block->y, $block->z, Block::SIDE_SOUTH),
					$this->getChunkManager()->getSide($block->x, $block->y, $block->z, Block::SIDE_WEST),
					$this->getChunkManager()->getSide($block->x, $block->y, $block->z, Block::SIDE_EAST),
				];

				$valid = 0;
				foreach($directions as $direction){
					if($direction->getId() !== Item::AIR){
						$valid++;
					}
				}
				if($valid >= 2){
					$blocks[] = $block;
				}
				if($valid >= 4){
					$oneHoles[] = $block;
				}
			}
		}
		foreach($blocks as $selectedBlock){
			$bottom = $this->getChunkManager()->getSide($selectedBlock->x, $selectedBlock->y, $selectedBlock->z, Block::SIDE_DOWN);
			$top = $this->getChunkManager()->getSide($selectedBlock->x, $selectedBlock->y, $selectedBlock->z, Block::SIDE_UP);
			$this->putBlock($selectedBlock, $bottom->getId() === Block::AIR ? $top->getId() : $bottom->getId(), $bottom->getId() === Block::AIR ? $top->getDamage() : $bottom->getDamage());
		}
		foreach($oneHoles as $block){
			$bottom = $this->getChunkManager()->getSide($block->x, $block->y, $block->z, Block::SIDE_DOWN);
			$east = $this->getChunkManager()->getSide($block->x, $block->y, $block->z, Block::SIDE_UP);
			$this->putBlock($block, $bottom->getId() === Block::AIR ? $east->getId() : $bottom->getId(), $bottom->getId() === Block::AIR ? $east->getDamage() : $bottom->getDamage());
		}
	}

	public function getName() : string{
		return "Expand";
	}
}
