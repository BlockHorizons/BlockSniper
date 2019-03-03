<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\brush\types;

use BlockHorizons\BlockSniper\brush\BaseType;
use pocketmine\block\Block;
use pocketmine\block\Flowable;
use pocketmine\math\Facing;

/*
 * Lays a layer of snow on top of the terrain, and raises it if there is snow already.
 */

class SnowConeType extends BaseType{

	public const ID = self::TYPE_SNOW_CONE;

	/**
	 * @return \Generator
	 */
	public function fill() : \Generator{
		foreach($this->blocks as $block){
			if(!($block instanceof Flowable) && ($id = $block->getId()) !== Block::AIR && $id !== Block::SNOW_LAYER){
				$topBlock = $this->side($block, Facing::UP);
				if(($topId = $topBlock->getId()) === Block::AIR || $topId === Block::SNOW_LAYER){
					if($topBlock->getMeta() < 7 && $topBlock->getId() === Block::SNOW_LAYER){
						yield $topBlock;
						$this->putBlock($topBlock, Block::get(Block::SNOW_LAYER, $topBlock->getMeta() + 1));
					}elseif($topId !== Block::SNOW_LAYER){
						yield $topBlock;
						$this->putBlock($topBlock, Block::get(Block::SNOW_LAYER));
					}
				}
			}
		}
	}

	/**
	 * @return string
	 */
	public function getName() : string{
		return "Snow Cone";
	}

	/**
	 * @return bool
	 */
	public function usesBlocks() : bool{
		return false;
	}
}
