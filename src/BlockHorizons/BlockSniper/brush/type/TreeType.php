<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\brush\type;

use BlockHorizons\BlockSniper\brush\BrushProperties;
use BlockHorizons\BlockSniper\brush\Target;
use BlockHorizons\BlockSniper\brush\Type;
use Generator;
use pocketmine\world\Position;

/*
 * Grows a custom tree on the target block.
 */

class TreeType extends Type{

	public const ID = self::TYPE_TREE;

	/** @var Tree */
	private $tree;

	public function __construct(BrushProperties $properties, Target $target, Generator $blocks = null){
		parent::__construct($properties, $target, $blocks);
		$this->tree = new Tree(Position::fromObject($target->asVector3(), $target->getChunkManager()), $properties, $this);
	}

	/**
	 * @return Generator
	 */
	public function fill() : Generator{
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
	public function usesBrushBlocks() : bool{
		return false;
	}

	/**
	 * @return string
	 */
	public function getName() : string{
		return "Tree";
	}
}