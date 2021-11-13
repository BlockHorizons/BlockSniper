<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\brush\type;

use BlockHorizons\BlockSniper\brush\Type;
use Generator;
use pocketmine\block\Air;
use pocketmine\block\Block;
use pocketmine\math\Facing;

/*
 * Expands the terrain with blocks below it.
 */

class ExpandType extends Type{

	public function fill() : Generator{
		$undoBlocks = [];
		foreach($this->mustGetBlocks() as $block){
			/** @var Block $block */
			if($block instanceof Air){
				$closedSides = 0;
				foreach(Facing::ALL as $direction){
					$sideBlock = $this->side($block->getPosition(), $direction);
					if(!($sideBlock instanceof Air)){
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
			$this->putBlock($selectedBlock->getPosition(), $this->randomBrushBlock());
		}
	}

	/**
	 * @return string
	 */
	public function getName() : string{
		return "Expand";
	}
}
