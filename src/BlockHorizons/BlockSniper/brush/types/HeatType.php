<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\brush\types;

use BlockHorizons\BlockSniper\brush\Type;
use Exception;
use Generator;
use pocketmine\block\Block;
use pocketmine\block\DoublePlant;
use pocketmine\block\Flower;
use pocketmine\block\Leaves;
use pocketmine\block\TallGrass;

class HeatType extends Type{

	public const ID = self::TYPE_HEAT;

	/**
	 * @return Generator
	 * @throws Exception
	 */
	public function fill() : Generator{
		foreach($this->blocks as $block){
			switch($block->getId()){
				case Block::PACKED_ICE:
				case Block::ICE:
					yield $block;
					$this->putBlock($block, Block::get(Block::WATER));
					break;
				case Block::SNOW_LAYER:
				case Block::SNOW:
					yield $block;
					$this->delete($block);
					break;
				case Block::WATER:
				case Block::FLOWING_WATER:
				case $block instanceof Leaves:
					if(random_int(0, 4) === 0){
						yield $block;
						$this->delete($block);
					}
					break;
				case Block::GRASS:
					$random = random_int(0, 8);
					if($random === 0){
						yield $block;
						$this->putBlock($block, Block::get(Block::DIRT));
					}elseif($random === 1){
						yield $block;
						$this->putBlock($block, Block::get(Block::DIRT, 1));
					}
					break;
				case $block instanceof Flower || $block instanceof DoublePlant || $block instanceof TallGrass:
					yield $block;
					$this->putBlock($block, Block::get(Block::DEAD_BUSH));
			}
		}
	}

	/**
	 * @return string
	 */
	public function getName() : string{
		return "Heat";
	}

	/**
	 * @return bool
	 */
	public function usesBrushBlocks() : bool{
		return false;
	}
}