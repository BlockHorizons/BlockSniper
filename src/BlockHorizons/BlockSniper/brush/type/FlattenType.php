<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\brush\type;

use BlockHorizons\BlockSniper\brush\Type;
use Generator;
use pocketmine\block\Block;
use pocketmine\block\Flowable;

/*
 * Flattens the terrain below the selected point within the brush radius.
 */

class FlattenType extends Type{

	public const ID = self::TYPE_FLATTEN;

	/**
	 * @return Generator
	 */
	public function fill() : Generator{
		foreach($this->blocks as $block){
			if($block->y <= $this->target->y && ($block->getId() === Block::AIR || $block instanceof Flowable)){
				yield $block;
				$this->putBlock($block, $this->randomBrushBlock());
			}
		}
	}

	/**
	 * @return string
	 */
	public function getName() : string{
		return "Flatten";
	}
}
