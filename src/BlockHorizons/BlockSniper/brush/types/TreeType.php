<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\brush\types;

use BlockHorizons\BlockSniper\brush\BaseType;
use BlockHorizons\BlockSniper\brush\Brush;
use BlockHorizons\BlockSniper\brush\TreeProperties;
use pocketmine\level\ChunkManager;

/*
 * Grows a custom tree on the target block.
 */

class TreeType extends BaseType{

	public const ID = self::TYPE_TREE;

	/** @var Tree*/
	private $tree;

	public function __construct(Brush $brush, ChunkManager $level, \Generator $blocks = null){
		parent::__construct($brush, $level, $blocks);
		$center = $brush->getPlayer()->getTargetBlock($brush->getPlayer()->getViewDistance() * 16);
		$this->tree = new Tree($center, $brush, $this);
	}

	/**
	 * @return \Generator
	 */
	public function fill() : \Generator{
		foreach($this->tree->build() as $block){
			yield $block;
		}
	}

	/**
	 * @return bool
	 */
	public function canBeExecutedAsynchronously() : bool{
		return false;
	}

	/**
	 * @return bool
	 */
	public function canBeHollow() : bool{
		return false;
	}

	/**
	 * @return bool
	 */
	public function usesSize() : bool{
		return false;
	}

	/**
	 * @return bool
	 */
	public function usesBlocks() : bool{
		return false;
	}

	/**
	 * @return string
	 */
	public function getName() : string{
		return "Tree";
	}

	/**
	 * Returns the tree properties of this type.
	 *
	 * @return TreeProperties
	 */
	public function getTree() : TreeProperties{
		return $this->brush->tree;
	}
}