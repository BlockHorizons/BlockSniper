<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\brush\types;

use BlockHorizons\BlockSniper\brush\BaseType;
use BlockHorizons\BlockSniper\brush\Brush;
use pocketmine\block\Air;
use pocketmine\block\Block;
use pocketmine\level\ChunkManager;
use pocketmine\math\Facing;

class PlantType extends BaseType{
	public const ID = self::TYPE_PLANT;

	/** @var Block[] */
	private $soilBlocks;

	public function __construct(Brush $brush, ChunkManager $manager, \Generator $blocks = null){
		parent::__construct($brush, $manager, $blocks);
		$this->soilBlocks = $brush->getSoil();
	}

	/**
	 * @return \Generator
	 */
	protected function fill() : \Generator{
		foreach($this->blocks as $block) {
			foreach($this->soilBlocks as $soil){
				if($block->getId() !== $soil->getId() || $block->getMeta() !== $soil->getMeta()){
					continue;
				}
				$blockUp = $this->side($block, Facing::UP);
				if($blockUp instanceof Air) {
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