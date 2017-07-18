<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockSniper\brush\types;

use BlockHorizons\BlockSniper\brush\BaseType;
use pocketmine\block\Block;
use pocketmine\level\ChunkManager;
use pocketmine\level\Level;
use pocketmine\Player;

class SnowconeType extends BaseType {
	
	/*
	 * Lays a layer of snow on top of the terrain, and raises it if there is snow already.
	 */
	public function __construct(Player $player, ChunkManager $level, array $blocks) {
		parent::__construct($player, $level, $blocks);
	}
	
	/**
	 * @return array
	 */
	public function fillShape(): array {
		$undoBlocks = [];
		foreach($this->blocks as $block) {
			if($block->getId() !== Block::AIR && $block->getId() !== Block::SNOW_LAYER) {
				if($this->isAsynchronous()) {
					$topBlock = $this->getChunkManager()->getSide($block->x, $block->y, $block->z, Block::SIDE_UP);
				} else {
					$topBlock = $block->getSide(Block::SIDE_UP);
				}
				if($topBlock->getId() === Block::AIR || $topBlock->getId() === Block::SNOW_LAYER) {
					if($topBlock->getDamage() < 7 && $topBlock->getId() === Block::SNOW_LAYER) {
						$undoBlocks[] = $topBlock;
						if($this->isAsynchronous()) {
							$this->getChunkManager()->setBlockDataAt($topBlock->x, $topBlock->y, $topBlock->z, $this->getChunkManager()->getBlockDataAt($topBlock->x, $topBlock->y, $topBlock->z) + 1);
						} else {
							$this->getLevel()->setBlockDataAt($topBlock->x, $topBlock->y, $topBlock->z, $topBlock->getDamage() + 1);
						}
					} elseif($topBlock->getId() !== Block::SNOW_LAYER) {
						$undoBlocks[] = $topBlock;
						if($this->isAsynchronous()) {
							$this->getChunkManager()->setBlockIdAt($topBlock->x, $topBlock->y, $topBlock->z, Block::SNOW_LAYER);
							$this->getChunkManager()->setBlockDataAt($topBlock->x, $topBlock->y, $topBlock->z, 0);
						} else {
							$this->getLevel()->setBlock($topBlock, Block::get(Block::SNOW_LAYER), false, false);
						}
					}
				}
			}
		}
		return $undoBlocks;
	}
	
	public function getName(): string {
		return "Snow Cone";
	}
}
