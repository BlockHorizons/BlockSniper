<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockSniper\brush\types;

use BlockHorizons\BlockSniper\brush\BaseType;
use pocketmine\block\Block;
use pocketmine\item\Item;
use pocketmine\level\Level;
use pocketmine\math\Vector3;
use pocketmine\Player;

class DrainType extends BaseType {
	
	/*
	 * Removes all liquid blocks within the brush radius.
	 */
	public function __construct(Player $player, Level $level, array $blocks) {
		parent::__construct($player, $level, $blocks);
	}
	
	/**
	 * @return array
	 */
	public function fillShape(): array {
		$undoBlocks = [];
		foreach($this->blocks as $block) {
			$blockId = $block->getId();
			if($blockId === Item::LAVA || $blockId === Item::WATER|| $blockId === Item::STILL_LAVA || $blockId === Item::STILL_WATER) {
				$undoBlocks[] = $block;
				$this->level->setBlock(new Vector3($block->x, $block->y, $block->z), Block::get(Block::AIR), false, false);
			}
		}
		return $undoBlocks;
	}
	
	public function getName(): string {
		return "Drain";
	}
}