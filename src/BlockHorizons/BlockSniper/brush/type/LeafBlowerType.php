<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\brush\type;

use BlockHorizons\BlockSniper\brush\Type;
use Generator;
use pocketmine\block\Flowable;

/*
 * Blows away all plants and flowers within the brush radius.
 */

class LeafBlowerType extends Type{

	public const ID = self::TYPE_LEAF_BLOWER;

	/**
	 * @return Generator
	 */
	public function fill() : Generator{
		foreach($this->blocks as $block){
			if($block instanceof Flowable){
				yield $block;
				$this->delete($block);
			}
		}
	}

	/**
	 * @return string
	 */
	public function getName() : string{
		return "Leaf Blower";
	}

	/**
	 * @return bool
	 */
	public function usesBrushBlocks() : bool{
		return false;
	}
}
