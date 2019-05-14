<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\brush\types;

use BlockHorizons\BlockSniper\brush\Type;

/*
 * Places blocks on every location within the brush radius.
 */

class FillType extends Type{

	public const ID = self::TYPE_FILL;

	/**
	 * @return \Generator
	 */
	public function fill() : \Generator{
		foreach($this->blocks as $block){
			yield $block;
			$this->putBlock($block, $this->randomBrushBlock());
		}
	}

	/**
	 * @return string
	 */
	public function getName() : string{
		return "Fill";
	}
}
