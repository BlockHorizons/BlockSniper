<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\brush\types;

use BlockHorizons\BlockSniper\brush\Type;
use Generator;
use pocketmine\block\Block;
use pocketmine\math\Facing;

class SmoothType extends Type{

	public const ID = self::TYPE_SMOOTH;

	/**
	 * @return Generator
	 */
	protected function fill() : Generator{
		$meltBlocks = [];
		$expandBlocks = [];
		foreach($this->blocks as $block){
			/** @var Block $block */
			switch($block->getId()){
				case Block::AIR:
					$closedSides = 0;
					foreach(Facing::ALL as $direction){
						$sideBlock = $this->side($block, $direction);
						if($sideBlock->getId() !== Block::AIR){
							$closedSides++;
						}
					}
					if($closedSides > 3){
						$expandBlocks[] = $block;
					}
					break;
				default:
					$openSides = 0;
					foreach(Facing::ALL as $direction){
						if($this->side($block, $direction)->getId() === Block::AIR){
							$openSides++;
						}
					}
					if($openSides > 3){
						$meltBlocks[] = $block;
					}
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