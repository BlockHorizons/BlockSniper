<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\brush\types;

use BlockHorizons\BlockSniper\brush\BaseType;
use pocketmine\item\Item;
use pocketmine\math\Facing;

/*
 * Melts away every block with more than 2 open sides within the brush radius.
 */

class MeltType extends BaseType{

	public const ID = self::TYPE_MELT;

	/**
	 * @return \Generator
	 */
	public function fillSynchronously() : \Generator{
		$blocks = [];
		foreach($this->blocks as $block){
			if($block->getId() !== Item::AIR){
				$valid = 0;
				foreach(Facing::ALL as $direction){
					if($block->getSide($direction)->getId() === Item::AIR){
						$valid++;
					}
				}
				if($valid >= 2){
					$blocks[] = $block;
				}
			}
		}
		foreach($blocks as $block){
			yield $block;
			$this->delete($block);
		}
	}

	public function fillAsynchronously() : void{
		$blocks = [];
		foreach($this->blocks as $block){
			if($block->getId() !== Item::AIR){
				$valid = 0;
				foreach(Facing::ALL as $direction){
					$sideBlock = $this->getChunkManager()->getSide($block->x, $block->y, $block->z, $direction);
					if($sideBlock->getId() === Item::AIR){
						$valid++;
					}
				}
				if($valid >= 2){
					$blocks[] = $block;
				}
			}
		}
		foreach($blocks as $selectedBlock){
			$this->delete($selectedBlock);
		}
	}

	public function getName() : string{
		return "Melt";
	}
}