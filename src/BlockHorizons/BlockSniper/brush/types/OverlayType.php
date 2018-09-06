<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\brush\types;

use BlockHorizons\BlockSniper\brush\BaseType;
use pocketmine\block\Block;
use pocketmine\item\Item;

/*
 * Lays a layer of blocks over every block within the brush radius.
 */

class OverlayType extends BaseType{

	const ID = self::TYPE_OVERLAY;

	/**
	 * @return \Generator
	 */
	public function fillSynchronously() : \Generator{
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
				$valid = true;
				foreach($this->brushBlocks as $possibleBlock){
					if($block->getId() === $possibleBlock->getId() && $block->getDamage() === $possibleBlock->getDamage()){
						$valid = false;
					}
				}
				foreach($directions as $direction){
					if($valid && $this->getLevel()->getBlock($direction)->getId() === Item::AIR){
						$randomBlock = $this->brushBlocks[array_rand($this->brushBlocks)];
						if($block->getId() !== $randomBlock->getId()){
							yield $direction;
							$this->putBlock($direction, $randomBlock->getId(), $randomBlock->getDamage());
						}
					}
				}
			}
		}
	}

	public function fillAsynchronously() : void{
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
				$valid = true;
				foreach($this->brushBlocks as $possibleBlock){
					if($block->getId() === $possibleBlock->getId() && $block->getDamage() === $possibleBlock->getDamage()){
						$valid = false;
					}
				}
				foreach($directions as $direction){
					if($valid && $this->getChunkManager()->getBlockIdAt($direction->x, $direction->y, $direction->z) === Item::AIR){
						$randomBlock = $this->brushBlocks[array_rand($this->brushBlocks)];
						if($block->getId() !== $randomBlock->getId()){
							$this->putBlock($direction, $randomBlock->getId(), $randomBlock->getDamage());
						}
					}
				}
			}
		}
	}

	public function getName() : string{
		return "Overlay";
	}
}
