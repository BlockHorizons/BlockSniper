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

	public function fill() : Generator{
		foreach($this->mustGetBlocks() as $block){
			if($block->getPosition()->y !== $this->target->y + 1){
				continue;
			}
			yield $block;
			$vec = new Vector3($block->getPosition()->x, $this->target->y + 1, $block->getPosition()->z);
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

