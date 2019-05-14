<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\brush\types;

use BlockHorizons\BlockSniper\brush\BaseType;
use BlockHorizons\BlockSniper\brush\BrushProperties;
use BlockHorizons\BlockSniper\brush\Target;
use pocketmine\block\Block;

/*
 * Replaces the obsolete blocks within the brush radius.
 */

class ReplaceType extends BaseType{

	public const ID = self::TYPE_REPLACE;

	/** @var Block[] */
	private $obsolete;

	public function __construct(BrushProperties $properties, Target $target, \Generator $blocks = null){
		parent::__construct($properties, $target, $blocks);
		$this->obsolete = $properties->getReplacedBlocks();
	}

	/**
	 * @return \Generator
	 */
	public function fill() : \Generator{
		foreach($this->blocks as $block){
			foreach($this->obsolete as $obsolete){
				if($block->getId() === $obsolete->getId() && $block->getMeta() === $obsolete->getMeta()){
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
		return "Replace";
	}

	/**
	 * Returns the obsolete blocks of this type.
	 *
	 * @return Block[]
	 */
	public function getObsolete() : array{
		return $this->obsolete;
	}
}

