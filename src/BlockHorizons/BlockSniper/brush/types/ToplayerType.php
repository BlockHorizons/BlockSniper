<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockSniper\brush\types;

use BlockHorizons\BlockSniper\brush\BaseType;
use BlockHorizons\BlockSniper\brush\BrushManager;
use pocketmine\block\Block;
use pocketmine\block\Flowable;
use pocketmine\item\Item;
use pocketmine\level\ChunkManager;
use pocketmine\math\Vector3;
use pocketmine\Player;

class ToplayerType extends BaseType {

	/*
	 * Replaces the top layer of the terrain, thickness depending on brush height, within the brush radius.
	 */
	public function __construct(Player $player, ChunkManager $level, array $blocks) {
		parent::__construct($player, $level, $blocks);
		$this->height = BrushManager::get($player)->getHeight();
	}

	/**
	 * @return array
	 */
	public function fillShape(): array {
		$undoBlocks = [];
		foreach($this->blocks as $block) {
			if($block->getId() !== Item::AIR && !$block instanceof Flowable) {
				if($this->isAsynchronous()) {
					$up = $this->getChunkManager()->getSide($block->x, $block->y, $block->z, Block::SIDE_UP);
				} else {
					$up = $block->getSide(Block::SIDE_UP);
				}
				if($up->getId() === Item::AIR || $up instanceof Flowable) {
					$randomBlock = $this->brushBlocks[array_rand($this->brushBlocks)];
					for($y = $block->y; $y >= $block->y - $this->height; $y--) {
						$undoBlocks[] = $this->getLevel()->getBlock(new Vector3($block->x, $y, $block->z));
						if($this->isAsynchronous()) {
							$this->getChunkManager()->setBlockIdAt($block->x, $block->y, $block->z, $randomBlock->getId());
							$this->getChunkManager()->setBlockDataAt($block->x, $block->y, $block->z, $randomBlock->getDamage());
						} else {
							$this->getLevel()->setBlock($block, $randomBlock, false, false);
						}
					}
				}
			}
		}
		return $undoBlocks;
	}

	public function getName(): string {
		return "Top Layer";
	}

	/**
	 * @return int
	 */
	public function getHeight(): int {
		return $this->height;
	}
}
