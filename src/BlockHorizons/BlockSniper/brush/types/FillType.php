<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\brush\types;

use BlockHorizons\BlockSniper\brush\BaseType;

/*
 * Places blocks on every location within the brush radius.
 */

class FillType extends BaseType{

	const ID = self::TYPE_FILL;

	/**
	 * @return \Generator
	 */
	public function fillSynchronously() : \Generator{
		foreach($this->blocks as $block){
			$randomBlock = $this->brushBlocks[array_rand($this->brushBlocks)];
			yield $block;
			$this->putBlock($block, $randomBlock->getId(), $randomBlock->getDamage());
		}
	}

	public function fillAsynchronously() : void{
		foreach($this->blocks as $block){
			$randomBlock = $this->brushBlocks[array_rand($this->brushBlocks)];
			$this->putBlock($block, $randomBlock->getId(), $randomBlock->getDamage());
		}
	}

	public function getName() : string{
		return "Fill";
	}
}
