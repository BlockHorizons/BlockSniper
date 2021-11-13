<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\brush\type;

use BlockHorizons\BlockSniper\brush\Type;
use Generator;
use pocketmine\block\Air;
use pocketmine\block\BlockLegacyIds;
use pocketmine\math\Facing;

/*
 * Lays a layer of blocks over every block within the brush radius.
 */

class OverlayType extends Type{

	public function fill() : Generator{
		foreach($this->mustGetBlocks() as $block){
			if($block->getId() !== BlockLegacyIds::AIR){
				$valid = true;
				foreach($this->brushBlocks as $possibleBlock){
					if($block->getId() === $possibleBlock->getId() && $block->getMeta() === $possibleBlock->getMeta()){
						$valid = false;
					}
				}
				foreach(Facing::ALL as $direction){
					$sideBlock = $this->side($block->getPosition(), $direction);
					if($valid && $sideBlock instanceof Air){
						$randomBlock = $this->brushBlocks[array_rand($this->brushBlocks)];
						if($block->getId() !== $randomBlock->getId() && $block->getMeta() !== $randomBlock->getMeta()){
							yield $sideBlock;
							$this->putBlock($sideBlock->getPosition(), $randomBlock);
						}
					}
				}
			}
		}
	}

	/**
	 * @return string
	 */
	public function getName() : string{
		return "Overlay";
	}
}
