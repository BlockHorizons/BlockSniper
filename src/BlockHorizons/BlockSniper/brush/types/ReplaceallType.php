<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockSniper\brush\types;

use BlockHorizons\BlockSniper\brush\BaseType;
use BlockHorizons\BlockSniper\brush\BrushManager;
use pocketmine\block\Block;
use pocketmine\block\Flowable;
use pocketmine\level\ChunkManager;
use pocketmine\level\Level;
use pocketmine\math\Vector3;
use pocketmine\Player;

class ReplaceallType extends BaseType {
	
	/*
	 * Replaces every solid block within the brush radius.
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
			if($block->getId() !== Block::AIR && !$block instanceof Flowable) {
				$randomBlock = $this->brushBlocks[array_rand($this->brushBlocks)];
				$undoBlocks[] = $block;
				if($this->isAsynchronous()) {
					$this->getChunkManager()->setBlockIdAt($block->x, $block->y, $block->z, $randomBlock->getId());
					$this->getChunkManager()->setBlockDataAt($block->x, $block->y, $block->z, $randomBlock->getDamage());
				} else {
					$this->getLevel()->setBlock($block, $randomBlock, false, false);
				}
			}
		}
		return $undoBlocks;
	}
	
	public function getName(): string {
		return "Replace All";
	}
}