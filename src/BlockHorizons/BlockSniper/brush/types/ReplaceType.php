<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockSniper\brush\types;

use BlockHorizons\BlockSniper\brush\BaseType;
use BlockHorizons\BlockSniper\brush\BrushManager;
use pocketmine\level\ChunkManager;
use pocketmine\Player;

class ReplaceType extends BaseType {

	/*
	 * Replaces the obsolete blocks within the brush radius.
	 */
	public function __construct(Player $player, ChunkManager $level, array $blocks) {
		parent::__construct($player, $level, $blocks);
		$this->obsolete = BrushManager::get($player)->getObsolete();
	}

	/**
	 * @return array
	 */
	public function fillShape(): array {
		$undoBlocks = [];
		foreach($this->blocks as $block) {
			$randomBlock = $this->brushBlocks[array_rand($this->brushBlocks)];
			foreach($this->obsolete as $obsolete) {
				if($block->getId() === $obsolete->getId()) {
					if($block->getId() !== $randomBlock->getId()) {
						$undoBlocks[] = $block;
					}
					if($this->isAsynchronous()) {
						$this->getChunkManager()->setBlockIdAt($block->x, $block->y, $block->z, $randomBlock->getId());
						$this->getChunkManager()->setBlockDataAt($block->x, $block->y, $block->z, $randomBlock->getDamage());
					} else {
						$this->getLevel()->setBlock($block, $randomBlock, false, false);
					}
				}
			}
		}
		return $undoBlocks;
	}

	public function getName(): string {
		return "Replace";
	}

	/**
	 * Returns the obsolete blocks of this type.
	 *
	 * @return array
	 */
	public function getObsolete(): array {
		return $this->obsolete;
	}
}

