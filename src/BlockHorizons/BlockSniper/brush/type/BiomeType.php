<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\brush\type;

use BlockHorizons\BlockSniper\brush\Type;
use Generator;

/*
 * Changes the biome within the brush radius.
 */

class BiomeType extends Type{

	protected function fill() : Generator{
		foreach($this->mustGetBlocks() as $block){
			$this->putBiome($block->getPosition(), $this->properties->biomeId);
		}
		// Make PHP recognize this is a generator.
		yield from [];
	}

	/**
	 * @return string
	 */
	public function getName() : string{
		return "Biome";
	}

	/**
	 * @return bool
	 */
	public function usesBrushBlocks() : bool{
		return false;
	}
}
