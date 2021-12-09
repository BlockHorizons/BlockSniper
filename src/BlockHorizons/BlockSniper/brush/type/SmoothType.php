<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\brush\type;

use BlockHorizons\BlockSniper\brush\Type;
use Generator;
use pocketmine\block\Air;
use pocketmine\block\Block;
use pocketmine\math\Facing;

class SmoothType extends Type{

	protected function fill() : Generator{
		$meltBlocks = [];
		$expandBlocks = [];
		foreach($this->mustGetBlocks() as $block){
			/** @var Block $block */
			if($block instanceof Air){
				$closedSides = 0;
				foreach(Facing::ALL as $direction){
					$sideBlock = $this->side($block->getPosition(), $direction);
					if(!($sideBlock instanceof Air)){
						$closedSides++;
					}
				}
				if($closedSides > 3){
					$expandBlocks[] = $block;
				}
				continue;
			}
			$openSides = 0;
			foreach(Facing::ALL as $direction){
				if($this->side($block->getPosition(), $direction) instanceof Air){
					$openSides++;
				}
			}
			if($openSides > 3){
				$meltBlocks[] = $block;
			}
		}
		foreach($meltBlocks as $block){
			yield $block;
			$this->delete($block->getPosition());
		}
		foreach($expandBlocks as $block){
			yield $block;
			$this->putBlock($block->getPosition(), $this->randomBrushBlock());
		}
	}

	/**
	 * @return string
	 */
	public function getName() : string{
		return "Smooth";
	}
}