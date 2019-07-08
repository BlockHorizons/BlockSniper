<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\brush\type;

use BlockHorizons\BlockSniper\brush\Type;
use Generator;
use pocketmine\block\Air;
use pocketmine\block\Block;
use pocketmine\block\Flowable;
use pocketmine\block\SnowLayer;
use pocketmine\math\Facing;

/*
 * Lays a layer of snow on top of the terrain, and raises it if there is snow already.
 */

class SnowConeType extends Type{

	/**
	 * @return Generator
	 */
	public function fill() : Generator{
		foreach($this->blocks as $block){
			if(!($block instanceof Flowable) && !($block instanceof Air) && !($block instanceof SnowLayer)){
				$topBlock = $this->side($block, Facing::UP);
				if($topBlock instanceof Air || $topBlock instanceof SnowLayer){
					if($topBlock->getMeta() < 7 && $topBlock->getId() === Block::SNOW_LAYER){
						yield $topBlock;
						$this->putBlock($topBlock, Block::get(Block::SNOW_LAYER, $topBlock->getMeta() + 1));
					}elseif(!($topBlock instanceof SnowLayer)){
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
	public function usesBrushBlocks() : bool{
		return false;
	}
}
