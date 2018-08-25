<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\brush\types;

use BlockHorizons\BlockSniper\brush\BaseType;
use pocketmine\block\Block;
use pocketmine\block\Flowable;

/*
 * Replaces every solid block within the brush radius.
 */

class ReplaceAllType extends BaseType{

	const ID = self::TYPE_REPLACE_ALL;

	/**
	 * @return Block[]
	 */
	public function fillSynchronously() : array{
		$undoBlocks = [];
		foreach($this->blocks as $block){
			if(!$block instanceof Flowable && $block->getId() !== Block::AIR){
				$randomBlock = $this->brushBlocks[array_rand($this->brushBlocks)];
				$undoBlocks[] = $block;
				$this->putBlock($block, $randomBlock->getId(), $randomBlock->getDamage());
			}
		}

		return $undoBlocks;
	}

	public function fillAsynchronously() : void{
		foreach($this->blocks as $block){
			if(!$block instanceof Flowable && $block->getId() !== Block::AIR){
				$randomBlock = $this->brushBlocks[array_rand($this->brushBlocks)];
				$this->putBlock($block, $randomBlock->getId(), $randomBlock->getDamage());
			}
		}
	}

	public function getName() : string{
		return "Replace All";
	}
}