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

	public function fill() : Generator{
		foreach($this->mustGetBlocks() as $block){
			if($block->getPosition()->y <= $this->target->y && ($block instanceof Air || $block instanceof Flowable)){
				yield $block;
				$this->putBlock($block->getPosition(), $this->randomBrushBlock());
			}
			if($block->getPosition()->y > $this->target->y && !($block instanceof Air)){
				yield $block;
				$this->delete($block->getPosition());
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
