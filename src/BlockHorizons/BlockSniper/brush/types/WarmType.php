<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\brush\types;

use BlockHorizons\BlockSniper\brush\BaseType;
use pocketmine\block\Block;

class WarmType extends BaseType{

	public const ID = self::TYPE_WARM;

	/**
	 * @return \Generator
	 */
	public function fill() : \Generator{
		foreach($this->blocks as $block){
			switch($block->getId()){
				case Block::ICE:
					yield $block;
					$this->putBlock($block, Block::get(Block::WATER));
					break;
				case Block::SNOW_LAYER:
					yield $block;
					$this->delete($block);
					break;
				case Block::PACKED_ICE:
					yield $block;
					$this->putBlock($block, Block::get(Block::ICE));
			}
		}
	}

	/**
	 * @return string
	 */
	public function getName() : string{
		return "Warm";
	}

	/**
	 * @return bool
	 */
	public function usesBrushBlocks() : bool{
		return false;
	}
}