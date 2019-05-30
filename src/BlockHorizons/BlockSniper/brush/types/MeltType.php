<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\brush\types;

use BlockHorizons\BlockSniper\brush\Type;
use Generator;
use pocketmine\block\Block;
use pocketmine\math\Facing;

/*
 * Melts away every block with more than 2 open sides within the brush radius.
 */

class MeltType extends Type{

	public const ID = self::TYPE_MELT;

	/**
	 * @return Generator
	 */
	public function fill() : Generator{
		$blocks = [];
		foreach($this->blocks as $block){
			if($block->getId() !== Block::AIR){
				$openSides = 0;
				foreach(Facing::ALL as $direction){
					if($this->side($block, $direction)->getId() === Block::AIR){
						$openSides++;
					}
				}
				if($openSides >= 2){
					$blocks[] = $block;
				}
			}
		}
		foreach($blocks as $block){
			yield $block;
			$this->delete($block);
		}
	}

	/**
	 * @return string
	 */
	public function getName() : string{
		return "Melt";
	}

	/**
	 * @return bool
	 */
	public function usesBrushBlocks() : bool{
		return false;
	}
}