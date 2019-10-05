<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\brush\type;

use BlockHorizons\BlockSniper\brush\Type;
use Generator;

/*
 * Changes the biome within the brush radius.
 */

class BiomeType extends Type{

	/**
	 * @return Generator
	 */
	protected function fill() : Generator{
		foreach($this->blocks as $block){
			$this->putBiome($block->getPos(), $this->properties->biomeId);
		}
		if(false){
			// Make PHP recognize this is a generator.
			yield;
		}
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
