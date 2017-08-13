<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockSniper\brush\types;

use BlockHorizons\BlockSniper\brush\BaseType;
use pocketmine\block\Block;
use pocketmine\item\Item;
use pocketmine\level\ChunkManager;
use pocketmine\math\Vector3;
use pocketmine\Player;

class DrainType extends BaseType {

	/** @var int */
	protected $id = self::TYPE_DRAIN;

	/*
	 * Removes all liquid blocks within the brush radius.
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
			$blockId = $block->getId();
			if($blockId === Item::LAVA || $blockId === Item::WATER || $blockId === Item::STILL_LAVA || $blockId === Item::STILL_WATER) {
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

	public function getName(): string {
		return "Drain";
	}
}