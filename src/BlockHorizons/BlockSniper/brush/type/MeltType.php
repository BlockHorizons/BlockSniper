<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\brush\type;

use BlockHorizons\BlockSniper\brush\Type;
use Generator;
use pocketmine\block\Air;
use pocketmine\block\BlockLegacyIds;
use pocketmine\math\Facing;

/*
 * Melts away every block with more than 2 open sides within the brush radius.
 */

class MeltType extends Type{

	/**
	 * @return Generator
	 */
	public function fill() : Generator{
		$blocks = [];
		foreach($this->blocks as $block){
			if($block->getId() !== BlockLegacyIds::AIR){
				$openSides = 0;
				foreach(Facing::ALL as $direction){
					if($this->side($block, $direction) instanceof Air){
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