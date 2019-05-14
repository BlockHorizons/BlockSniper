<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\brush\types;

use BlockHorizons\BlockSniper\brush\Type;
use pocketmine\block\Block;

/*
 * Replaces every solid block within the brush radius.
 */

class ReplaceAllType extends Type{

	public const ID = self::TYPE_REPLACE_ALL;

	/**
	 * @return \Generator
	 */
	public function fill() : \Generator{
		foreach($this->blocks as $block){
			if($block->getId() !== Block::AIR){
				yield $block;
				$this->putBlock($block, $this->randomBrushBlock());
			}
		}
	}

	/**
	 * @return string
	 */
	public function getName() : string{
		return "Replace All";
	}
}