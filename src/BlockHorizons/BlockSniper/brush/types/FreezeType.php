<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\brush\types;

use BlockHorizons\BlockSniper\brush\Type;
use Generator;
use pocketmine\block\Block;

/*
 * Freezes the terrain, causing water to become ice, lava to become obsidian and extinguishes fire.
 */

class FreezeType extends Type{

	public const ID = self::TYPE_FREEZE;

	/**
	 * @return Generator
	 */
	public function fill() : Generator{
		foreach($this->blocks as $block){
			switch($block->getId()){
				case Block::WATER:
				case Block::FLOWING_WATER:
					yield $block;
					$this->putBlock($block, Block::get(Block::ICE));
					break;
				case Block::LAVA:
				case Block::FLOWING_LAVA:
					yield $block;
					$this->putBlock($block, Block::get(Block::OBSIDIAN));
					break;
				case Block::FIRE:
					yield $block;
					$this->delete($block);
					break;
				case Block::ICE:
					yield $block;
					$this->putBlock($block, Block::get(Block::PACKED_ICE));
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