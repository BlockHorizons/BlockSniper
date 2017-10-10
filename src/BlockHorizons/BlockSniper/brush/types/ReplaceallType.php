<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockSniper\brush\types;

use BlockHorizons\BlockSniper\brush\BaseType;
use pocketmine\block\Block;
use pocketmine\block\Flowable;
use pocketmine\level\ChunkManager;
use pocketmine\Player;

class ReplaceallType extends BaseType {

	/** @var int */
	protected $id = self::TYPE_REPLACEALL;

	/*
	 * Replaces every solid block within the brush radius.
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
			if($block->getId() !== Block::AIR && !$block instanceof Flowable) {
				$randomBlock = $this->brushBlocks[array_rand($this->brushBlocks)];
				$undoBlocks[] = $block;
				$this->getLevel()->setBlock($block, $randomBlock, false, false);
			}
		}
		return $undoBlocks;
	}

	public function fillAsynchronously(): void {
		foreach($this->blocks as $block) {
			if($block->getId() !== Block::AIR && !$block instanceof Flowable) {
				$randomBlock = $this->brushBlocks[array_rand($this->brushBlocks)];
				$this->getChunkManager()->setBlockIdAt($block->x, $block->y, $block->z, $randomBlock->getId());
				$this->getChunkManager()->setBlockDataAt($block->x, $block->y, $block->z, $randomBlock->getDamage());
			}
		}
	}

	public function getName(): string {
		return "Replace All";
	}
}