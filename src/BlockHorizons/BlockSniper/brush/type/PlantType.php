<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\brush\type;

use BlockHorizons\BlockSniper\brush\Type;
use Generator;
use pocketmine\block\Air;
use pocketmine\block\Block;
use pocketmine\math\Facing;

class PlantType extends Type{
	public const ID = self::TYPE_PLANT;

	/** @var Block[] */
	private $soilBlocks;

	/**
	 * @return Generator
	 */
	protected function fill() : Generator{
		$this->soilBlocks = $this->properties->getSoilBlocks();
		foreach($this->blocks as $block){
			foreach($this->soilBlocks as $soil){
				if($block->getId() !== $soil->getId() || $block->getMeta() !== $soil->getMeta()){
					continue;
				}
				$blockUp = $this->side($block, Facing::UP);
				if($blockUp instanceof Air){
					yield $block;
					$this->putBlock($blockUp, $this->randomBrushBlock());
					break;
				}
			}
		}
	}

	/**
	 * @return string
	 */
	public function getName() : string{
		return "Plant";
	}
}