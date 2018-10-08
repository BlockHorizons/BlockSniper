<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\brush\types;

use BlockHorizons\BlockSniper\brush\BaseType;
use pocketmine\item\Item;
use pocketmine\math\Facing;

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
				$valid = true;
				foreach($this->brushBlocks as $possibleBlock){
					if($block->getId() === $possibleBlock->getId() && $block->getDamage() === $possibleBlock->getDamage()){
						$valid = false;
					}
				}
				foreach(Facing::ALL as $direction){
					$sideBlock = $block->getSide($direction);
					if($valid && $sideBlock->getId() === Item::AIR){
						$randomBlock = $this->brushBlocks[array_rand($this->brushBlocks)];
						if($block->getId() !== $randomBlock->getId()){
							yield $sideBlock;
							$this->putBlock($sideBlock, $randomBlock->getId(), $randomBlock->getDamage());
						}
					}
				}
			}
		}
	}

	public function fillAsynchronously() : void{
		foreach($this->blocks as $block){
			if($block->getId() !== Item::AIR){
				$valid = true;
				foreach($this->brushBlocks as $possibleBlock){
					if($block->getId() === $possibleBlock->getId() && $block->getDamage() === $possibleBlock->getDamage()){
						$valid = false;
					}
				}
				foreach(Facing::ALL as $direction){
					$sideBlock = $this->getChunkManager()->getSide($block->x, $block->y, $block->z, $direction);
					if($valid && $sideBlock === Item::AIR){
						$randomBlock = $this->brushBlocks[array_rand($this->brushBlocks)];
						if($block->getId() !== $randomBlock->getId()){
							$this->putBlock($sideBlock, $randomBlock->getId(), $randomBlock->getDamage());
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
