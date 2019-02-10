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
 * Flattens the terrain below the selected point and removes the blocks above it within the brush radius.
 */

class FlattenAllType extends BaseType{

	public const ID = self::TYPE_FLATTEN_ALL;

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
			if($block->y > $this->center->y && $block->getId() !== Block::AIR){
				yield $block;
				$this->delete($block);
			}
		}
	}

	/**
	 * @return string
	 */
	public function getName() : string{
		return "Flatten All";
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
