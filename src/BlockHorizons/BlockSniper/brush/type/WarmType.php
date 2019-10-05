<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\brush\type;

use BlockHorizons\BlockSniper\brush\Type;
use Generator;
use pocketmine\block\BlockFactory;
use pocketmine\block\BlockLegacyIds;
use pocketmine\block\VanillaBlocks;

class WarmType extends Type{

	/**
	 * @return Generator
	 */
	public function fill() : Generator{
		foreach($this->blocks as $block){
			switch($block->getId()){
				case BlockLegacyIds::ICE:
					yield $block;
					$this->putBlock($block->getPos(), VanillaBlocks::WATER());
					break;
				case BlockLegacyIds::SNOW_LAYER:
					yield $block;
					$this->delete($block->getPos());
					break;
				case BlockLegacyIds::PACKED_ICE:
					yield $block;
					$this->putBlock($block->getPos(), VanillaBlocks::ICE());
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