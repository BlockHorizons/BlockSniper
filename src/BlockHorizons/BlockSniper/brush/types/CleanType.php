<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockSniper\brush\types;

use BlockHorizons\BlockSniper\brush\BaseType;
use pocketmine\block\Block;
use pocketmine\level\ChunkManager;
use pocketmine\math\Vector3;
use pocketmine\Player;

class CleanType extends BaseType {

	/** @var int */
	protected $id = self::TYPE_CLEAN;

	/*
	 * Removes all non-natural blocks within the brush radius.
	 */
	public function __construct(Player $player, ChunkManager $level, array $blocks) {
		parent::__construct($player, $level, $blocks);
	}

	/**
	 * @return Block[]|null
	 */
	public function fillShape(): ?array {
		if($this->isAsynchronous()) {
			$this->fillAsynchronously();
			return null;
		}
		$undoBlocks = [];
		foreach($this->blocks as $block) {
			$blockId = $block->getId();
			if($blockId !== Block::AIR && $blockId !== Block::STONE && $blockId !== Block::GRASS && $blockId !== Block::DIRT && $blockId !== Block::GRAVEL && $blockId !== Block::SAND && $blockId !== Block::SANDSTONE) {
				$undoBlocks[] = $block;
				if($this->isAsynchronous()) {
					$this->getChunkManager()->setBlockIdAt($block->x, $block->y, $block->z, Block::AIR);
				} else {
					$this->getLevel()->setBlock(new Vector3($block->x, $block->y, $block->z), Block::get(Block::AIR), false, false);
				}
			}
		}
		return $undoBlocks;
	}

	public function fillAsynchronously(): void {
		foreach($this->blocks as $block) {
			$blockId = $block->getId();
			if($blockId !== Block::AIR && $blockId !== Block::STONE && $blockId !== Block::GRASS && $blockId !== Block::DIRT && $blockId !== Block::GRAVEL && $blockId !== Block::SAND && $blockId !== Block::SANDSTONE) {
				$this->getChunkManager()->setBlockIdAt($block->x, $block->y, $block->z, Block::AIR);
			}
		}
	}

	public function getName(): string {
		return "Clean";
	}
}
