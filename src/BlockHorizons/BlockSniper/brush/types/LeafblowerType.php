<?php

namespace BlockHorizons\BlockSniper\brush\types;

use BlockHorizons\BlockSniper\brush\BaseType;
use BlockHorizons\BlockSniper\undo\UndoStorer;
use pocketmine\block\Block;
use pocketmine\block\Flowable;
use pocketmine\item\Item;
use pocketmine\level\Level;
use pocketmine\Player;

class LeafblowerType extends BaseType {
	
	/*
	 * Blows away all plants and flowers within the brush radius.
	 */
	public function __construct(UndoStorer $undoStorer, Player $player, Level $level, array $blocks) {
		parent::__construct($undoStorer, $player, $level, $blocks);
	}

	/**
	 * @return array
	 */
	public function fillShape(): array {
		$undoBlocks = [];
		foreach($this->blocks as $block) {
			if($block instanceof Flowable) {
				$undoBlocks[] = $block;
				$loader = $this->getUndoStorer()->getLoader();
				if($loader->getSettings()->get("Drop-Leafblower-Plants")) {
					$this->level->dropItem($block, Item::get($block->getId()));
				}
				$this->level->setBlock($block, Block::get(Block::AIR), false, false);
			}
		}
		return $undoBlocks;
	}
	
	public function getName(): string {
		return "Leaf Blower";
	}
}
