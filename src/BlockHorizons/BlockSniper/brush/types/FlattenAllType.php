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
 * Flattens the terrain below the selected point and removes the blocks above it within the brush radius.
 */

class FlattenAllType extends BaseType{

	const ID = self::TYPE_FLATTEN_ALL;

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
			if($block->y > $this->center->y && $block->getId() !== Item::AIR){
				yield $block;
				$this->putBlock($block, 0);
			}
		}
	}

	public function fillAsynchronously() : void{
		foreach($this->blocks as $block){
			$randomBlock = $this->brushBlocks[array_rand($this->brushBlocks)];
			if($block->y <= $this->center->y && ($block->getId() === Item::AIR || $block instanceof Flowable)){
				$this->putBlock($block, $randomBlock->getId(), $randomBlock->getDamage());
			}
			if($block->y > $this->center->y && $block->getId() !== Item::AIR){
				$this->putBlock($block, 0);
			}
		}
	}

	public function getName() : string{
		return "Flatten All";
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
