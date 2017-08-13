<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockSniper\brush\types;

use BlockHorizons\BlockSniper\brush\BaseType;
use pocketmine\block\Block;
use pocketmine\item\Item;
use pocketmine\level\ChunkManager;
use pocketmine\Player;

class OverlayType extends BaseType {

	/** @var int */
	protected $id = self::TYPE_OVERLAY;

	/*
	 * Lays a layer of blocks over every block within the brush radius.
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
			if($block->getId() !== Item::AIR) {
				$directions = [
					$block->getSide(Block::SIDE_DOWN),
					$block->getSide(Block::SIDE_UP),
					$block->getSide(Block::SIDE_NORTH),
					$block->getSide(Block::SIDE_SOUTH),
					$block->getSide(Block::SIDE_WEST),
					$block->getSide(Block::SIDE_EAST)
				];
				if($this->isAsynchronous()) {
					$directions = [
						$this->getChunkManager()->getSide($block->x, $block->y, $block->z, Block::SIDE_DOWN),
						$this->getChunkManager()->getSide($block->x, $block->y, $block->z, Block::SIDE_UP),
						$this->getChunkManager()->getSide($block->x, $block->y, $block->z, Block::SIDE_NORTH),
						$this->getChunkManager()->getSide($block->x, $block->y, $block->z, Block::SIDE_SOUTH),
						$this->getChunkManager()->getSide($block->x, $block->y, $block->z, Block::SIDE_WEST),
						$this->getChunkManager()->getSide($block->x, $block->y, $block->z, Block::SIDE_EAST),
					];
				}
				$valid = true;
				foreach($this->brushBlocks as $possibleBlock) {
					if($block->getId() === $possibleBlock->getId() && $block->getDamage() === $possibleBlock->getDamage()) {
						$valid = false;
					}
				}
				foreach($directions as $direction) {
					if($this->getLevel()->getBlock($direction)->getId() === Item::AIR && $valid) {
						$randomBlock = $this->brushBlocks[array_rand($this->brushBlocks)];
						if($block->getId() !== $randomBlock->getId()) {
							$undoBlocks[] = $direction;
							if($this->isAsynchronous()) {
								$this->getChunkManager()->setBlockIdAt($block->x, $this->center->y + 1, $block->z, $randomBlock->getId());
								$this->getChunkManager()->setBlockDataAt($block->x, $this->center->y + 1, $block->z, $randomBlock->getDamage());
							} else {
								$this->getLevel()->setBlock($direction, $randomBlock, false, false);
							}
						}
					}
				}
			}
		}
		return $undoBlocks;
	}

	public function getName(): string {
		return "Overlay";
	}
}
