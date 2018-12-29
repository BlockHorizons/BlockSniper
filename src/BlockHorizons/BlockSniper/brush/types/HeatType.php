<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\brush\types;

use BlockHorizons\BlockSniper\brush\BaseType;
use pocketmine\block\Block;
use pocketmine\block\Dandelion;
use pocketmine\block\DoublePlant;
use pocketmine\block\Flower;
use pocketmine\block\Leaves;
use pocketmine\block\TallGrass;

class HeatType extends BaseType{

	public const ID = self::TYPE_HEAT;

	/**
	 * @return \Generator
	 * @throws \Exception
	 */
	public function fill() : \Generator{
		foreach($this->blocks as $block){
			switch($block->getId()){
				case Block::ICE:
					yield $block;
					$this->putBlock($block, Block::WATER);
					break;
				case Block::SNOW_LAYER:
				case Block::SNOW:
					yield $block;
					$this->delete($block);
					break;
				case Block::PACKED_ICE:
					yield $block;
					$this->putBlock($block, Block::WATER);
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
						$this->putBlock($block, Block::DIRT);
					}elseif($random === 1){
						yield $block;
						$this->putBlock($block, Block::DIRT, 1);
					}
					break;
				case $block instanceof Flower || $block instanceof DoublePlant || $block instanceof TallGrass || $block instanceof Dandelion:
					yield $block;
					$this->putBlock($block, Block::TALL_GRASS);
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
	public function usesBlocks() : bool{
		return false;
	}
}