<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockSniper\brush\types;

use BlockHorizons\BlockSniper\brush\BaseType;
use pocketmine\block\Block;
use pocketmine\level\ChunkManager;
use pocketmine\Player;

class SnowConeType extends BaseType {

	const ID = self::TYPE_SNOW_CONE;

	/*
	 * Lays a layer of snow on top of the terrain, and raises it if there is snow already.
	 */
	public function __construct(Player $player, ChunkManager $level, array $blocks) {
		parent::__construct($player, $level, $blocks);
	}

	/**
	 * @return Block[]
	 */
	public function fillSynchronously(): array {
		$undoBlocks = [];
		foreach($this->blocks as $block) {
			if(($id = $block->getId()) !== Block::AIR && $id !== Block::SNOW_LAYER) {
				$topBlock = $block->getSide(Block::SIDE_UP);
				if(($topId = $topBlock->getId()) === Block::AIR || $topId === Block::SNOW_LAYER) {
					if($topBlock->getDamage() < 7 && $topBlock->getId() === Block::SNOW_LAYER) {
						$undoBlocks[] = $topBlock;
						$this->getLevel()->setBlockDataAt($topBlock->x, $topBlock->y, $topBlock->z, $topBlock->getDamage() + 1);
					} elseif($topId !== Block::SNOW_LAYER) {
						$undoBlocks[] = $topBlock;
						$this->getLevel()->setBlock($topBlock, Block::get(Block::SNOW_LAYER), false, false);
					}
				}
			}
		}
		return $undoBlocks;
	}

	public function fillAsynchronously(): void {
		foreach($this->blocks as $block) {
			if(($id = $block->getId()) !== Block::AIR && $id !== Block::SNOW_LAYER) {
				$topBlock = $this->getChunkManager()->getSide($block->x, $block->y, $block->z, Block::SIDE_UP);
				if(($topId = $topBlock->getId()) === Block::AIR || $topId === Block::SNOW_LAYER) {
					if($topBlock->getDamage() < 7 && $topBlock->getId() === Block::SNOW_LAYER) {
						$this->getChunkManager()->setBlockDataAt($topBlock->x, $topBlock->y, $topBlock->z, $this->getChunkManager()->getBlockDataAt($topBlock->x, $topBlock->y, $topBlock->z) + 1);
					} elseif($topId !== Block::SNOW_LAYER) {
						$this->getChunkManager()->setBlockIdAt($topBlock->x, $topBlock->y, $topBlock->z, Block::SNOW_LAYER);
						$this->getChunkManager()->setBlockDataAt($topBlock->x, $topBlock->y, $topBlock->z, 0);
					}
				}
			}
		}
	}

	public function getName(): string {
		return "Snow Cone";
	}
}
