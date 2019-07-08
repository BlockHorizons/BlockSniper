<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\brush\type;

use BlockHorizons\BlockSniper\brush\BrushProperties;
use BlockHorizons\BlockSniper\brush\Target;
use BlockHorizons\BlockSniper\brush\Type;
use Generator;
use pocketmine\block\Block;

class ReplaceTargetType extends Type{

	/** @var int */
	private $targetBlockId;
	/** @var int */
	private $targetBlockMeta;

	public function __construct(BrushProperties $properties, Target $target, Generator $blocks = null){
		parent::__construct($properties, $target, $blocks);
		$this->targetBlockId = $this->getBlock($this->target)->getId();
		$this->targetBlockMeta = $this->getBlock($this->target)->getMeta();
	}

	/**
	 * @return Generator
	 */
	public function fill() : Generator{
		/** @var Block $block */
		$targetBlock = Block::get($this->targetBlockId, $this->targetBlockMeta);
		foreach($this->blocks as $block){
			if($block->getId() === $targetBlock->getId() && $block->getMeta() === $targetBlock->getMeta()){
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