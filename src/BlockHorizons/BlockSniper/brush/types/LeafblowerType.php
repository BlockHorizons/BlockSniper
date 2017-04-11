<?php

namespace BlockHorizons\BlockSniper\brush\types;

use BlockHorizons\BlockSniper\brush\BaseType;
use BlockHorizons\BlockSniper\Loader;
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
	 * @return bool
	 */
	public function fillShape(): bool {
		$undoBlocks = [];
		foreach($this->blocks as $block) {
			if($block instanceof Flowable) {
				$undoBlocks[] = $block;
				/** @var Loader $loader */
				$loader = $this->getLevel()->getServer()->getPluginManager()->getPlugin("BlockSniper");
				if($loader->getSettings()->get("Drop-Leafblower-Plants")) {
					$this->level->dropItem($block, Item::get($block->getId()));
				}
				$this->level->setBlock($block, Block::get(Block::AIR), false, false);
			}
		}
		$this->getUndoStore()->saveUndo($undoBlocks, $this->player);
		return true;
	}
	
	public function getName(): string {
		return "Leaf Blower";
	}
}
