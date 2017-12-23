<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockSniper\brush\types;

use BlockHorizons\BlockSniper\brush\BaseType;
use pocketmine\block\Block;
use pocketmine\block\Flowable;

/*
 * Lays a layer of snow on top of the terrain, and raises it if there is snow already.
 */

class SnowConeType extends BaseType {

	const ID = self::TYPE_SNOW_CONE;

	/**
	 * @return Block[]
	 */
	public function fillSynchronously(): array {
		$undoBlocks = [];
		foreach($this->blocks as $block) {
			if(!($block instanceof Flowable) && ($id = $block->getId()) !== Block::AIR && $id !== Block::SNOW_LAYER) {
				$topBlock = $block->getSide(Block::SIDE_UP);
				if(($topId = $topBlock->getId()) === Block::AIR || $topId === Block::SNOW_LAYER) {
					if($topBlock->getDamage() < 7 && $topBlock->getId() === Block::SNOW_LAYER) {
						$undoBlocks[] = $topBlock;
						$this->putBlock($topBlock, $topBlock->getId(), $topBlock->getDamage() + 1);
					} elseif($topId !== Block::SNOW_LAYER) {
						$undoBlocks[] = $topBlock;
						$this->putBlock($topBlock, Block::SNOW_LAYER);
					}
				}
			}
		}
		return $undoBlocks;
	}

	public function fillAsynchronously(): void {
		foreach($this->blocks as $block) {
			if(!($block instanceof Flowable) && ($id = $block->getId()) !== Block::AIR && $id !== Block::SNOW_LAYER) {
				$topBlock = $this->getChunkManager()->getSide($block->x, $block->y, $block->z, Block::SIDE_UP);
				if(($topId = $topBlock->getId()) === Block::AIR || $topId === Block::SNOW_LAYER) {
					if($topBlock->getDamage() < 7 && $topBlock->getId() === Block::SNOW_LAYER) {
						$this->putBlock($topBlock, $topBlock->getId(), $topBlock->getDamage() + 1);
					} elseif($topId !== Block::SNOW_LAYER) {
						$this->putBlock($topBlock, Block::SNOW_LAYER);
					}
				}
			}
		}
	}

	public function getName(): string {
		return "Snow Cone";
	}
}
