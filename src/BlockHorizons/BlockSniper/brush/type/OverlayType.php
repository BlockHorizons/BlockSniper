<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\brush\type;

use BlockHorizons\BlockSniper\brush\Type;
use Generator;
use pocketmine\block\Block;
use pocketmine\math\Facing;

/*
 * Lays a layer of blocks over every block within the brush radius.
 */

class OverlayType extends Type{

	public const ID = self::TYPE_OVERLAY;

	/**
	 * @return Generator
	 */
	public function fill() : Generator{
		foreach($this->blocks as $block){
			if($block->getId() !== Block::AIR){
				$valid = true;
				foreach($this->brushBlocks as $possibleBlock){
					if($block->getId() === $possibleBlock->getId() && $block->getMeta() === $possibleBlock->getMeta()){
						$valid = false;
					}
				}
				foreach(Facing::ALL as $direction){
					$sideBlock = $this->side($block, $direction);
					if($valid && $sideBlock->getId() === Block::AIR){
						$randomBlock = $this->brushBlocks[array_rand($this->brushBlocks)];
						if($block->getId() !== $randomBlock->getId() && $block->getMeta() !== $randomBlock->getMeta()){
							yield $sideBlock;
							$this->putBlock($sideBlock, $randomBlock);
						}
					}
				}
			}
		}
	}

	/**
	 * @return string
	 */
	public function getName() : string{
		return "Overlay";
	}
}
