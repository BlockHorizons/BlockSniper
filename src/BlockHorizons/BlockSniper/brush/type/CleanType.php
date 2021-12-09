<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\brush\type;

use BlockHorizons\BlockSniper\brush\Type;
use Generator;
use pocketmine\block\BlockLegacyIds;

/*
 * Removes all non-natural blocks within the brush radius.
 */

class CleanType extends Type{

	private const NATURAL_BLOCKS = [
		BlockLegacyIds::STONE => 0,
		BlockLegacyIds::GRASS => 0,
		BlockLegacyIds::DIRT => 0,
		BlockLegacyIds::GRAVEL => 0,
		BlockLegacyIds::SAND => 0,
		BlockLegacyIds::SANDSTONE => 0
	];

	protected function fill() : Generator{
		foreach($this->mustGetBlocks() as $block){
			if(!isset(self::NATURAL_BLOCKS[$block->getId()])){
				yield $block;
				$this->delete($block->getPosition());
			}
		}
	}

	/**
	 * @return string
	 */
	public function getName() : string{
		return "Clean";
	}

	/**
	 * @return bool
	 */
	public function usesBrushBlocks() : bool{
		return false;
	}
}
