<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\brush\types;

use BlockHorizons\BlockSniper\brush\BaseType;
use pocketmine\block\Block;
use pocketmine\level\ChunkManager;
use pocketmine\math\Vector3;
use pocketmine\Player;

/*
 * Lays a thin layer of blocks within the brush radius.
 */

class LayerType extends BaseType{

	const ID = self::TYPE_LAYER;

	public function __construct(Player $player, ChunkManager $level, array $blocks){
		parent::__construct($player, $level, $blocks);
		$this->center = $player->getTargetBlock(100)->asVector3();
	}

	/**
	 * @return Block[]
	 */
	public function fillSynchronously() : array{
		$undoBlocks = [];
		foreach($this->blocks as $block){
			$randomBlock = $this->brushBlocks[array_rand($this->brushBlocks)];
			$undoBlocks[] = $block;
			$vec = new Vector3($block->x, $this->center->y + 1, $block->z);
			$this->putBlock($vec, $randomBlock->getId(), $randomBlock->getDamage());
		}

		return $undoBlocks;
	}

	public function fillAsynchronously() : void{
		foreach($this->blocks as $block){
			$randomBlock = $this->brushBlocks[array_rand($this->brushBlocks)];
			$vec = new Vector3($block->x, $this->center->y + 1, $block->z);
			$this->putBlock($vec, $randomBlock->getId(), $randomBlock->getDamage());
		}
	}

	public function getName() : string{
		return "Layer";
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

