<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\brush\types;

use BlockHorizons\BlockSniper\brush\BaseType;
use BlockHorizons\BlockSniper\brush\Brush;
use pocketmine\block\Block;
use pocketmine\block\Flowable;
use pocketmine\level\ChunkManager;
use pocketmine\math\Vector3;

/*
 * Flattens the terrain below the selected point within the brush radius.
 */

class FlattenType extends BaseType{

	public const ID = self::TYPE_FLATTEN;

	public function __construct(Brush $brush, ChunkManager $level, \Generator $blocks = null){
		parent::__construct($brush, $level, $blocks);
		$this->center = $brush->getPlayer()->getTargetBlock($brush->getPlayer()->getViewDistance() * 16)->asVector3();
	}

	/**
	 * @return \Generator
	 */
	public function fill() : \Generator{
		foreach($this->blocks as $block){
			if($block->y <= $this->center->y && ($block->getId() === Block::AIR || $block instanceof Flowable)){
				yield $block;
				$this->putBlock($block, $this->randomBrushBlock());
			}
		}
	}

	/**
	 * @return string
	 */
	public function getName() : string{
		return "Flatten";
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
