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

	public function fill() : Generator{
		foreach($this->mustGetBlocks() as $block){
			if($block->getId() !== BlockLegacyIds::AIR){
				yield $block;
				$this->putBlock($block->getPosition(), $this->randomBrushBlock());
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