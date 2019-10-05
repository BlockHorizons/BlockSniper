<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\brush\type;

use BlockHorizons\BlockSniper\brush\Type;
use Generator;
use pocketmine\block\Air;
use pocketmine\block\Flowable;

/*
 * Flattens the terrain below the selected point and removes the blocks above it within the brush radius.
 */

class FlattenAllType extends Type{

	/**
	 * @return Generator
	 */
	public function fill() : Generator{
		foreach($this->blocks as $block){
			if($block->getPos()->y <= $this->target->y && ($block instanceof Air || $block instanceof Flowable)){
				yield $block;
				$this->putBlock($block->getPos(), $this->randomBrushBlock());
			}
			if($block->getPos()->y > $this->target->y && !($block instanceof Air)){
				yield $block;
				$this->delete($block->getPos());
			}
		}
	}

	/**
	 * @return string
	 */
	public function getName() : string{
		return "Flatten All";
	}
}
