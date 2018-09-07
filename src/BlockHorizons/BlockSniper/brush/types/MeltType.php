<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\brush\types;

use BlockHorizons\BlockSniper\brush\BaseType;
use pocketmine\block\Block;
use pocketmine\item\Item;

/*
 * Melts away every block with more than 2 open sides within the brush radius.
 */

class MeltType extends BaseType{

	const ID = self::TYPE_MELT;

	/**
	 * @return \Generator
	 */
	public function fillSynchronously() : \Generator{
		$blocks = [];
		foreach($this->blocks as $block){
			if($block->getId() !== Item::AIR){
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
					if($direction->getId() === Item::AIR){
						$valid++;
					}
				}
				if($valid >= 2){
					$blocks[] = $block;
				}
			}
		}
		foreach($blocks as $block){
			yield $block;
			$this->putBlock($block, 0);
		}
	}

	public function fillAsynchronously() : void{
		$blocks = [];
		foreach($this->blocks as $block){
			if($block->getId() !== Item::AIR){
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
					if($direction->getId() === Item::AIR){
						$valid++;
					}
				}
				if($valid >= 2){
					$blocks[] = $block;
				}
			}
		}
		foreach($blocks as $selectedBlock){
			$this->putBlock($selectedBlock, 0);
		}
	}

	public function getName() : string{
		return "Melt";
	}
}