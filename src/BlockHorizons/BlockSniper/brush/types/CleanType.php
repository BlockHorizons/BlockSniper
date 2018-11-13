<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\brush\types;

use BlockHorizons\BlockSniper\brush\BaseType;
use pocketmine\block\BlockIds;

/*
 * Removes all non-natural blocks within the brush radius.
 */

class CleanType extends BaseType{

	public const ID = self::TYPE_CLEAN;

	private const NATURAL_BLOCKS = [
		BlockIds::STONE => 0,
		BlockIds::GRASS => 0,
		BlockIds::DIRT => 0,
		BlockIds::GRAVEL => 0,
		BlockIds::SAND => 0,
		BlockIds::SANDSTONE => 0
	];

	public function getName() : string{
		return "Clean";
	}

	/**
	 * @return \Generator
	 */
	protected function fillSynchronously() : \Generator{
		foreach($this->blocks as $block){
			if(isset(self::NATURAL_BLOCKS[$block->getId()])){
				yield $block;
				$this->delete($block);
			}
		}
	}

	protected function fillAsynchronously() : void{
		foreach($this->blocks as $block){
			if(isset(self::NATURAL_BLOCKS[$block->getId()])){
				$this->delete($block);
			}
		}
	}
}
