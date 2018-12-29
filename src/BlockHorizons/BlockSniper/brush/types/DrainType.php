<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\brush\types;

use BlockHorizons\BlockSniper\brush\BaseType;
use pocketmine\item\Item;

/*
 * Removes all liquid blocks within the brush radius.
 */

class DrainType extends BaseType{

	public const ID = self::TYPE_DRAIN;

	private const LIQUID_BLOCKS = [
		Item::FLOWING_WATER => 0,
		Item::WATER => 0,
		Item::FLOWING_LAVA => 0,
		Item::LAVA => 0
	];

	/**
	 * @return \Generator
	 */
	public function fill() : \Generator{
		foreach($this->blocks as $block){
			if(isset(self::LIQUID_BLOCKS[$block->getId()])){
				yield $block;
				$this->delete($block);
			}
		}
	}

	/**
	 * @return string
	 */
	public function getName() : string{
		return "Drain";
	}

	/**
	 * @return bool
	 */
	public function usesBlocks() : bool{
		return false;
	}
}