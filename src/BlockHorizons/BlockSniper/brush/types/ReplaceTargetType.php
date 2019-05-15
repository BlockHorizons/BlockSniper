<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\brush\types;

use BlockHorizons\BlockSniper\brush\BrushProperties;
use BlockHorizons\BlockSniper\brush\Target;
use BlockHorizons\BlockSniper\brush\Type;
use pocketmine\block\Block;

class ReplaceTargetType extends Type{

	public const ID = self::TYPE_REPLACE_TARGET;

	/** @var Block */
	private $targetBlock;

	public function __construct(BrushProperties $properties, Target $target, \Generator $blocks = null){
		parent::__construct($properties, $target, $blocks);
		$this->targetBlock = $this->getBlock($this->target)->setWorld(null);
	}

	/**
	 * @return \Generator
	 */
	public function fill() : \Generator{
		/** @var Block $block */
		foreach($this->blocks as $block){
			if($block->getId() === $this->targetBlock->getId() && $block->getMeta() === $this->targetBlock->getMeta()){
				yield $block;
				$this->putBlock($block, $this->randomBrushBlock());
			}
		}
	}

	/**
	 * @return string
	 */
	public function getName() : string{
		return "Replace Target";
	}
}