<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\brush\types;

use BlockHorizons\BlockSniper\brush\BaseType;
use pocketmine\block\Flowable;
use pocketmine\item\Item;
use pocketmine\level\ChunkManager;
use pocketmine\math\Vector3;
use pocketmine\Player;

/*
 * Flattens the terrain below the selected point within the brush radius.
 */

class FlattenType extends BaseType{

	const ID = self::TYPE_FLATTEN;

	public function __construct(Player $player, ChunkManager $level, \Generator $blocks){
		parent::__construct($player, $level, $blocks);
		$this->center = $player->getTargetBlock(100)->asVector3();
	}

	/**
	 * @return \Generator
	 */
	public function fillSynchronously() : \Generator{
		foreach($this->blocks as $block){
			$randomBlock = $this->brushBlocks[array_rand($this->brushBlocks)];
			if($block->y <= $this->center->y && ($block->getId() === Item::AIR || $block instanceof Flowable)){
				yield $block;
				$this->putBlock($block, $randomBlock->getId(), $randomBlock->getDamage());
			}
		}
	}

	public function fillAsynchronously() : void{
		foreach($this->blocks as $block){
			$randomBlock = $this->brushBlocks[array_rand($this->brushBlocks)];
			if($block->y <= $this->center->y && ($block->getId() === Item::AIR || $block instanceof Flowable)){
				$this->putBlock($block, $randomBlock->getId(), $randomBlock->getDamage());
			}
		}
	}

	public function getName() : string{
		return "Flatten";
	}

	/**
	 * Returns the center of this type.
	 *
	 * @return Vector3
	 */
	public function getCenter() : Vector3{
		return $this->center;
	}
}
