<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\brush\type;

use BlockHorizons\BlockSniper\brush\Type;
use Generator;

/*
 * Places blocks on every location within the brush radius.
 */

class FillType extends Type{

	public function fill() : Generator{
		foreach($this->mustGetBlocks() as $block){
			yield $block;
			$this->putBlock($block->getPosition(), $this->randomBrushBlock());
		}
	}

	/**
	 * @return string
	 */
	public function getName() : string{
		return "Fill";
	}
}
