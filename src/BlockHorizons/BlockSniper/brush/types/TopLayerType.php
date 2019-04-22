<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\brush\types;

use BlockHorizons\BlockSniper\brush\BaseType;
use BlockHorizons\BlockSniper\brush\BrushProperties;
use BlockHorizons\BlockSniper\brush\Target;
use pocketmine\block\Block;
use pocketmine\block\BlockIds;
use pocketmine\block\Flowable;
use pocketmine\math\Facing;

/*
 * Replaces the top layer of the terrain, thickness depending on brush height, within the brush radius.
 */

class TopLayerType extends BaseType{

	public const ID = self::TYPE_TOP_LAYER;

	/** @var int */
	private $layerWidth;

	public function __construct(BrushProperties $properties, Target $target, \Generator $blocks = null){
		parent::__construct($properties, $target, $blocks);
		$this->layerWidth = $properties->layerWidth;
	}

	/**
	 * @return \Generator
	 */
	public function fill() : \Generator{
		foreach($this->blocks as $block){
			if($block instanceof Flowable || $block->getId() === Block::AIR){
				continue;
			}

			$higherBlock = $block;
			for($y = $block->y; $y <= $block->y + $this->layerWidth; $y++){
				$higherBlock = $this->side($higherBlock, Facing::UP);
				if($higherBlock instanceof Flowable || $higherBlock->getId() === BlockIds::AIR){
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

	/**
	 * Returns the height/width of the top layer.
	 *
	 * @return int
	 */
	public function getHeight() : int{
		return $this->height;
	}
}
