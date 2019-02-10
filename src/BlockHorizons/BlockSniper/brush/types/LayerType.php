<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\brush\types;

use BlockHorizons\BlockSniper\brush\BaseType;
use BlockHorizons\BlockSniper\brush\Brush;
use pocketmine\level\ChunkManager;
use pocketmine\math\Vector3;

/*
 * Lays a thin layer of blocks within the brush radius.
 */

class LayerType extends BaseType{

	public const ID = self::TYPE_LAYER;

	public function __construct(Brush $brush, ChunkManager $level, \Generator $blocks = null){
		parent::__construct($brush, $level, $blocks);
		$this->center = $brush->getPlayer()->getTargetBlock($brush->getPlayer()->getViewDistance() * 16)->asVector3();
	}

	/**
	 * @return \Generator
	 */
	public function fill() : \Generator{
		foreach($this->blocks as $block){
			if($block->y !== $this->center->y + 1){
				continue;
			}
			yield $block;
			$vec = new Vector3($block->x, $this->center->y + 1, $block->z);
			$this->putBlock($vec, $this->randomBrushBlock());
		}
	}

	/**
	 * @return string
	 */
	public function getName() : string{
		return "Layer";
	}

	/**
	 * Returns the center of this type.
	 *
	 * @return Vector3
	 */
	public function getCenter() : Vector3{
		return $this->center;
	}
}

