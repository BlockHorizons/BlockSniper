<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\brush\types;

use BlockHorizons\BlockSniper\brush\BaseType;
use pocketmine\item\Item;

/*
 * Removes all liquid blocks within the brush radius.
 */

class DrainType extends BaseType{

	const ID = self::TYPE_DRAIN;

	/**
	 * @return \Generator
	 */
	public function fillSynchronously() : \Generator{
		foreach($this->blocks as $block){
			$blockId = $block->getId();
			if($blockId === Item::LAVA || $blockId === Item::WATER || $blockId === Item::STILL_LAVA || $blockId === Item::STILL_WATER){
				yield $block;
				$this->putBlock($block, 0);
			}
		}
	}

	public function fillAsynchronously() : void{
		foreach($this->blocks as $block){
			$blockId = $block->getId();
			if($blockId === Item::LAVA || $blockId === Item::WATER || $blockId === Item::STILL_LAVA || $blockId === Item::STILL_WATER){
				$this->putBlock($block, 0);
			}
		}
	}

	public function getName() : string{
		return "Drain";
	}
}