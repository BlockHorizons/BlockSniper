<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\brush\type;

use BlockHorizons\BlockSniper\brush\Type;
use Generator;
use pocketmine\block\Block;

/*
 * Removes all liquid blocks within the brush radius.
 */

class DrainType extends Type{

	private const LIQUID_BLOCKS = [
		Block::FLOWING_WATER => 0,
		Block::WATER => 0,
		Block::FLOWING_LAVA => 0,
		Block::LAVA => 0
	];

	/**
	 * @return Generator
	 */
	public function fill() : Generator{
		foreach($this->blocks as $block){
			if(isset(self::LIQUID_BLOCKS[$block->getId()])){
				yield $block;
				$this->delete($block);
			}
		}
	}

	/**
	 * @return string
	 */
	public function getName() : string{
		return "Drain";
	}

	/**
	 * @return bool
	 */
	public function usesBrushBlocks() : bool{
		return false;
	}
}