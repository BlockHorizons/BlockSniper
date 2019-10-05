<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\brush\type;

use BlockHorizons\BlockSniper\brush\Type;
use Generator;
use pocketmine\math\Vector3;

/*
 * Lays a thin layer of blocks within the brush radius.
 */

class LayerType extends Type{

	/**
	 * @return Generator
	 */
	public function fill() : Generator{
		foreach($this->blocks as $block){
			if($block->y !== $this->target->y + 1){
				continue;
			}
			yield $block;
			$vec = new Vector3($block->getPos()->x, $this->target->y + 1, $block->getPos()->z);
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

