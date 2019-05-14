<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\brush\types;

use BlockHorizons\BlockSniper\brush\Type;
use pocketmine\math\Vector3;

/*
 * Lays a thin layer of blocks within the brush radius.
 */

class LayerType extends Type{

	public const ID = self::TYPE_LAYER;

	/**
	 * @return \Generator
	 */
	public function fill() : \Generator{
		foreach($this->blocks as $block){
			if($block->y !== $this->target->y + 1){
				continue;
			}
			yield $block;
			$vec = new Vector3($block->x, $this->target->y + 1, $block->z);
			$this->putBlock($vec, $this->randomBrushBlock());
		}
	}

	/**
	 * @return string
	 */
	public function getName() : string{
		return "Layer";
	}
}

