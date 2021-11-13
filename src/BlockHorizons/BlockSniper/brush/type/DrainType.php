<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\brush\type;

use BlockHorizons\BlockSniper\brush\Type;
use Generator;
use pocketmine\block\BlockLegacyIds;

/*
 * Removes all liquid blocks within the brush radius.
 */

class DrainType extends Type{

	private const LIQUID_BLOCKS = [
		BlockLegacyIds::FLOWING_WATER => 0,
		BlockLegacyIds::WATER => 0,
		BlockLegacyIds::FLOWING_LAVA => 0,
		BlockLegacyIds::LAVA => 0
	];

	public function fill() : Generator{
		foreach($this->mustGetBlocks() as $block){
			if(isset(self::LIQUID_BLOCKS[$block->getId()])){
				yield $block;
				$this->delete($block->getPosition());
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