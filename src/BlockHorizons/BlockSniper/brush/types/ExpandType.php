<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\brush\types;

use BlockHorizons\BlockSniper\brush\BaseType;
use pocketmine\block\Block;
use pocketmine\math\Facing;

/*
 * Expands the terrain with blocks below it.
 */

class ExpandType extends BaseType{

	public const ID = self::TYPE_EXPAND;

	/**
	 * @return \Generator
	 */
	public function fill() : \Generator{
		$undoBlocks = [];
		foreach($this->blocks as $block){
			/** @var Block $block */
			if($block->getId() === Block::AIR){
				$closedSides = 0;
				foreach(Facing::ALL as $direction){
					$sideBlock = $this->side($block, $direction);
					if($sideBlock->getId() !== Block::AIR){
						$closedSides++;
					}
				}
				if($closedSides >= 2){
					$undoBlocks[] = $block;
				}
			}
		}
		foreach($undoBlocks as $selectedBlock){
			yield $selectedBlock;
			$this->putBlock($selectedBlock, $this->randomBrushBlock());
		}
	}

	/**
	 * @return string
	 */
	public function getName() : string{
		return "Expand";
	}

	/**
	 * @return bool
	 */
	public function usesBlocks() : bool{
		return false;
	}
}
