<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\brush\types;

use BlockHorizons\BlockSniper\brush\BaseType;
use pocketmine\block\Block;
use pocketmine\item\Item;
use pocketmine\math\Facing;

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
			/** @var Block $block */
			if($block->getId() === Item::AIR){
				$valid = 0;
				foreach(Facing::ALL as $direction){
					$sideBlock = $block->getSide($direction);
					if($sideBlock->getId() !== Item::AIR){
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
			$undoBlock = ($selectedBlock->getSide(Facing::DOWN)->getId() === Block::AIR ? $selectedBlock->getSide(Facing::UP) : $selectedBlock->getSide(Facing::DOWN));
			yield $undoBlock;
			$this->putBlock($selectedBlock, $undoBlock->getId(), $undoBlock->getDamage());
		}
		foreach($oneHoles as $block){
			/** @var Block $oneHole */
			$oneHole = ($block->getSide(Facing::DOWN)->getId() === Block::AIR ? $block->getSide(Facing::EAST) : $block->getSide(Facing::DOWN));
			yield $oneHole;
			$this->putBlock($block, $oneHole->getId(), $oneHole->getDamage());
		}
	}

	public function fillAsynchronously() : void{
		$oneHoles = [];
		$blocks = [];
		foreach($this->blocks as $block){
			if($block->getId() === Item::AIR){
				$valid = 0;
				foreach(Facing::ALL as $direction){
					$sideBlock = $this->getChunkManager()->getSide($block->x, $block->y, $block->z, $direction);
					if($sideBlock->getId() !== Item::AIR){
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
			$bottom = $this->getChunkManager()->getSide($selectedBlock->x, $selectedBlock->y, $selectedBlock->z, Facing::DOWN);
			$top = $this->getChunkManager()->getSide($selectedBlock->x, $selectedBlock->y, $selectedBlock->z, Facing::UP);
			$this->putBlock($selectedBlock, $bottom->getId() === Block::AIR ? $top->getId() : $bottom->getId(), $bottom->getId() === Block::AIR ? $top->getDamage() : $bottom->getDamage());
		}
		foreach($oneHoles as $block){
			$bottom = $this->getChunkManager()->getSide($block->x, $block->y, $block->z, Facing::DOWN);
			$east = $this->getChunkManager()->getSide($block->x, $block->y, $block->z, Facing::EAST);
			$this->putBlock($block, $bottom->getId() === Block::AIR ? $east->getId() : $bottom->getId(), $bottom->getId() === Block::AIR ? $east->getDamage() : $bottom->getDamage());
		}
	}

	public function getName() : string{
		return "Expand";
	}
}
