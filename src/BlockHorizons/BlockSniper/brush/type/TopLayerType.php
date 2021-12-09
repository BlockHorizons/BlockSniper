<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\brush\type;

use BlockHorizons\BlockSniper\brush\Type;
use Generator;
use pocketmine\block\Air;
use pocketmine\block\Flowable;
use pocketmine\math\Facing;

/*
 * Replaces the top layer of the terrain, thickness depending on brush height, within the brush radius.
 */

class TopLayerType extends Type{

	public function fill() : Generator{
		foreach($this->mustGetBlocks() as $block){
			if($block instanceof Flowable || $block instanceof Air){
				continue;
			}

			$higherBlock = $block;
			for($y = $block->getPosition()->y, $maxY = $block->getPosition()->y + $this->properties->layerWidth; $y <= $maxY; $y++){
				$higherBlock = $this->side($higherBlock->getPosition(), Facing::UP);
				if($higherBlock instanceof Flowable || $higherBlock instanceof Air){
					yield $block;
					$this->putBlock($block->getPosition(), $this->randomBrushBlock());
					break;
				}
			}
		}
	}

	/**
	 * @return string
	 */
	public function getName() : string{
		return "Top Layer";
	}
}
