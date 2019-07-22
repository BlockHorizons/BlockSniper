<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\brush\type;

use BlockHorizons\BlockSniper\brush\Type;
use Generator;
use pocketmine\block\BlockFactory;
use pocketmine\block\BlockLegacyIds;
use pocketmine\block\VanillaBlocks;

/*
 * Freezes the terrain, causing water to become ice, lava to become obsidian and extinguishes fire.
 */

class FreezeType extends Type{

	/**
	 * @return Generator
	 */
	public function fill() : Generator{
		foreach($this->blocks as $block){
			switch($block->getId()){
				case BlockLegacyIds::WATER:
				case BlockLegacyIds::FLOWING_WATER:
					yield $block;
					$this->putBlock($block, VanillaBlocks::ICE());
					break;
				case BlockLegacyIds::LAVA:
				case BlockLegacyIds::FLOWING_LAVA:
					yield $block;
					$this->putBlock($block, VanillaBlocks::OBSIDIAN());
					break;
				case BlockLegacyIds::FIRE:
					yield $block;
					$this->delete($block);
					break;
				case BlockLegacyIds::ICE:
					yield $block;
					$this->putBlock($block, VanillaBlocks::PACKED_ICE());
			}
		}
	}

	/**
	 * @return string
	 */
	public function getName() : string{
		return "Freeze";
	}

	/**
	 * @return bool
	 */
	public function usesBrushBlocks() : bool{
		return false;
	}
}