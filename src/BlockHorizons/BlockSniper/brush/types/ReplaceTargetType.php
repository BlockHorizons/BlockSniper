<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\brush\types;

use BlockHorizons\BlockSniper\brush\BaseType;
use BlockHorizons\BlockSniper\brush\Brush;
use pocketmine\block\Block;
use pocketmine\level\ChunkManager;

class ReplaceTargetType extends BaseType {

	public const ID = self::TYPE_REPLACE_TARGET;

	/** @var Block */
	private $targetBlock;

	public function __construct(Brush $brush, ChunkManager $level, \Generator $blocks = null){
		parent::__construct($brush, $level, $blocks);
		$targetBlock = $brush->getPlayer()->getTargetBlock($brush->getPlayer()->getViewDistance() * 16);
		$this->targetBlock = Block::get($targetBlock->getId(), $targetBlock->getMeta());
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