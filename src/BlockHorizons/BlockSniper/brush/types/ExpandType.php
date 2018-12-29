<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\brush\types;

use BlockHorizons\BlockSniper\brush\BaseType;
use pocketmine\block\Block;
use pocketmine\item\Item;
use pocketmine\math\Facing;

/*
 * Expands the terrain with blocks below it.
 */

class ExpandType extends BaseType{

	public const ID = self::TYPE_EXPAND;

	/**
	 * @return \Generator
	 */
	public function fill() : \Generator{
		$undoBlocks = [];
		$oneHoles = [];
		foreach($this->blocks as $block){
			/** @var Block $block */
			if($block->getId() === Item::AIR){
				$valid = 0;
				foreach(Facing::ALL as $direction){
					$sideBlock = $this->side($block, $direction);
					if($sideBlock->getId() !== Item::AIR){
						$valid++;
					}
				}
				if($valid >= 2){
					$undoBlocks[] = $block;
				}
				if($valid >= 4){
					$oneHoles[] = $block;
				}
			}
		}
		foreach($undoBlocks as $selectedBlock){
			/** @var Block $undoBlock */
			$undoBlock = ($this->side($selectedBlock, Facing::DOWN)->getId() === Block::AIR ? $this->side($selectedBlock, Facing::UP) : $this->side($selectedBlock, Facing::DOWN));
			yield $undoBlock;
			$this->putBlock($selectedBlock, $undoBlock->getId(), $undoBlock->getDamage());
		}
		foreach($oneHoles as $block){
			/** @var Block $oneHole */
			$oneHole = ($this->side($block, Facing::DOWN)->getId() === Block::AIR ? $this->side($block, Facing::EAST) : $this->side($block, Facing::DOWN));
			yield $oneHole;
			$this->putBlock($block, $oneHole->getId(), $oneHole->getDamage());
		}
	}

	/**
	 * @return string
	 */
	public function getName() : string{
		return "Expand";
	}

	/**
	 * @return bool
	 */
	public function usesBlocks() : bool{
		return false;
	}
}
