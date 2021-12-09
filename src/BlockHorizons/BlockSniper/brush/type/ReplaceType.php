<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\brush\type;

use BlockHorizons\BlockSniper\brush\Type;
use Generator;
use pocketmine\block\Block;

/*
 * Replaces the obsolete blocks within the brush radius.
 */

class ReplaceType extends Type{

	/** @var Block[] */
	private $obsolete;

	public function fill() : Generator{
		$this->obsolete = $this->properties->getReplacedBlocks();
		foreach($this->mustGetBlocks() as $block){
			foreach($this->obsolete as $obsolete){
				if($block->getId() === $obsolete->getId() && $block->getMeta() === $obsolete->getMeta()){
					yield $block;
					$this->putBlock($block->getPosition(), $this->randomBrushBlock());
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
}

