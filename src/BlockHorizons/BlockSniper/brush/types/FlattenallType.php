<?php

namespace BlockHorizons\BlockSniper\brush\types;

use BlockHorizons\BlockSniper\brush\BaseType;
use BlockHorizons\BlockSniper\brush\BrushManager;
use pocketmine\block\Block;
use pocketmine\block\Flowable;
use pocketmine\item\Item;
use pocketmine\level\Level;
use pocketmine\math\Vector3;
use pocketmine\Player;

class FlattenallType extends BaseType {
	
	/*
	 * Flattens the terrain below the selected point and removes the blocks above it within the brush radius.
	 */
	public function __construct(Player $player, Level $level, array $blocks) {
		parent::__construct($player, $level, $blocks);
		$this->center = $player->getTargetBlock(100);
	}

	/**
	 * @return array
	 */
	public function fillShape(): array {
		$undoBlocks = [];
		foreach($this->blocks as $block) {
			$randomBlock = BrushManager::get($this->player)->getBlocks()[array_rand(BrushManager::get($this->player)->getBlocks())];
			if(($block->getId() === Item::AIR || $block instanceof Flowable) && $block->y <= $this->center->y) {
				if($block->getId() !== $randomBlock->getId()) {
					$undoBlocks[] = $block;
				}
				$this->level->setBlock(new Vector3($block->x, $block->y, $block->z), $randomBlock, false, false);
			}
			if($block->getId() !== Item::AIR && $block->y > $this->center->y) {
				$undoBlocks[] = $block;
				$this->level->setBlock($block, Block::get(Block::AIR));
			}
		}
		return $undoBlocks;
	}
	
	public function getName(): string {
		return "Flatten All";
	}
}
