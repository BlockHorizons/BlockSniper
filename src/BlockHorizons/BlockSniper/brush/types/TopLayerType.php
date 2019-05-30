<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\brush\types;

use BlockHorizons\BlockSniper\brush\BrushProperties;
use BlockHorizons\BlockSniper\brush\Target;
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

	/** @var int */
	private $layerWidth;

	public function __construct(BrushProperties $properties, Target $target, Generator $blocks = null){
		parent::__construct($properties, $target, $blocks);
		$this->layerWidth = $properties->layerWidth;
	}

	/**
	 * @return Generator
	 */
	public function fill() : Generator{
		foreach($this->blocks as $block){
			if($block instanceof Flowable || $block instanceof Air){
				continue;
			}

			$higherBlock = $block;
			for($y = $block->y; $y <= $block->y + $this->layerWidth; $y++){
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
