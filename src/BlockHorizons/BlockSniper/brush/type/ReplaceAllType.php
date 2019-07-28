<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\brush\type;

use BlockHorizons\BlockSniper\brush\Type;
use Generator;
use pocketmine\block\BlockLegacyIds;

/*
 * Replaces every solid block within the brush radius.
 */

class ReplaceAllType extends Type{

	/**
	 * @return Generator
	 */
	public function fill() : Generator{
		foreach($this->blocks as $block){
			if($block->getId() !== BlockLegacyIds::AIR){
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