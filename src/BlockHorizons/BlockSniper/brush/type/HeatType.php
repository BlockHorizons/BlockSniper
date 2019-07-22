<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\brush\type;

use BlockHorizons\BlockSniper\brush\Type;
use Exception;
use Generator;
use pocketmine\block\BlockFactory;
use pocketmine\block\BlockLegacyIds;
use pocketmine\block\DoublePlant;
use pocketmine\block\Flower;
use pocketmine\block\Leaves;
use pocketmine\block\TallGrass;
use pocketmine\block\VanillaBlocks;

class HeatType extends Type{

	/**
	 * @return Generator
	 * @throws Exception
	 */
	public function fill() : Generator{
		foreach($this->blocks as $block){
			switch($block->getId()){
				case BlockLegacyIds::PACKED_ICE:
				case BlockLegacyIds::ICE:
					yield $block;
					$this->putBlock($block, VanillaBlocks::WATER());
					break;
				case BlockLegacyIds::SNOW_LAYER:
				case BlockLegacyIds::SNOW:
					yield $block;
					$this->delete($block);
					break;
				case BlockLegacyIds::WATER:
				case BlockLegacyIds::FLOWING_WATER:
				case $block instanceof Leaves:
					if(random_int(0, 4) === 0){
						yield $block;
						$this->delete($block);
					}
					break;
				case BlockLegacyIds::GRASS:
					$random = random_int(0, 8);
					if($random === 0){
						yield $block;
						$this->putBlock($block, VanillaBlocks::DIRT());
					}elseif($random === 1){
						yield $block;
						$this->putBlock($block, BlockFactory::get(BlockLegacyIds::DIRT, 1));
					}
					break;
				case $block instanceof Flower || $block instanceof DoublePlant || $block instanceof TallGrass:
					yield $block;
					$this->putBlock($block, VanillaBlocks::DEAD_BUSH());
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