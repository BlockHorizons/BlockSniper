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

	public function fill() : Generator{
		$blocks = [];
		foreach($this->mustGetBlocks() as $block){
			if($block->getId() !== BlockLegacyIds::AIR){
				$openSides = 0;
				foreach(Facing::ALL as $direction){
					if($this->side($block->getPosition(), $direction) instanceof Air){
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
			$this->delete($block->getPosition());
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