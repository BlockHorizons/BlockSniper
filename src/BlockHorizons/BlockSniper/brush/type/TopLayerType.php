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

	public const ID = self::TYPE_TOP_LAYER;

	/**
	 * @return Generator
	 */
	public function fill() : Generator{
		foreach($this->blocks as $block){
			if($block instanceof Flowable || $block instanceof Air){
				continue;
			}

			$higherBlock = $block;
			for($y = $block->y; $y <= $block->y + $this->properties->layerWidth; $y++){
				$higherBlock = $this->side($higherBlock, Facing::UP);
				if($higherBlock instanceof Flowable || $higherBlock instanceof Air){
					yield $block;
					$this->putBlock($block, $this->randomBrushBlock());
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
