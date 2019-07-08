<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\brush\type;

use BlockHorizons\BlockSniper\brush\Type;
use Generator;
use pocketmine\block\Air;
use pocketmine\block\Block;
use pocketmine\math\Facing;

class SmoothType extends Type{

	/**
	 * @return Generator
	 */
	protected function fill() : Generator{
		$meltBlocks = [];
		$expandBlocks = [];
		foreach($this->blocks as $block){
			/** @var Block $block */
			if($block instanceof Air){
				$closedSides = 0;
				foreach(Facing::ALL as $direction){
					$sideBlock = $this->side($block, $direction);
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
				if($this->side($block, $direction) instanceof Air){
					$openSides++;
				}
			}
			if($openSides > 3){
				$meltBlocks[] = $block;
			}
		}
		foreach($meltBlocks as $block){
			yield $block;
			$this->delete($block);
		}
		foreach($expandBlocks as $block){
			yield $block;
			$this->putBlock($block, $this->randomBrushBlock());
		}
	}

	/**
	 * @return string
	 */
	public function getName() : string{
		return "Smooth";
	}
}