<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\brush\type;

use BlockHorizons\BlockSniper\brush\BrushProperties;
use BlockHorizons\BlockSniper\brush\Target;
use BlockHorizons\BlockSniper\brush\Type;
use Generator;
use pocketmine\block\Block;
use pocketmine\block\BlockFactory;

class ReplaceTargetType extends Type{

	/** @var int */
	private $targetBlockId;
	/** @var int */
	private $targetBlockMeta;

	/**
	 * @phpstan-param Generator<int, Block, void, void>|null  $blocks
	 */
	public function __construct(BrushProperties $properties, Target $target, Generator $blocks = null){
		parent::__construct($properties, $target, $blocks);
		$this->targetBlockId = $this->getBlock($this->target)->getId();
		$this->targetBlockMeta = $this->getBlock($this->target)->getMeta();
	}

	public function fill() : Generator{
		$targetBlock = BlockFactory::getInstance()->get($this->targetBlockId, $this->targetBlockMeta);
		/** @var Block $block */
		foreach($this->mustGetBlocks() as $block){
			if($block->getId() === $targetBlock->getId() && $block->getMeta() === $targetBlock->getMeta()){
				yield $block;
				$this->putBlock($block->getPosition(), $this->randomBrushBlock());
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