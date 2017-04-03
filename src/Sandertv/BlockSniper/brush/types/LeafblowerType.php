<?php

namespace Sandertv\BlockSniper\brush\types;

use pocketmine\block\Block;
use pocketmine\block\Flowable;
use pocketmine\item\Item;
use pocketmine\level\Level;
use pocketmine\Player;
use Sandertv\BlockSniper\brush\BaseType;
use Sandertv\BlockSniper\Loader;

class LeafblowerType extends BaseType {
	
	/*
	 * Blows away all plants and flowers within the brush radius.
	 */
	public function __construct(Loader $main, Player $player, Level $level, array $blocks = []) {
		parent::__construct($main);
		$this->level = $level;
		$this->player = $player;
		$this->blocks = $blocks;
	}
	
	/**
	 * @return bool
	 */
	public function fillShape(): bool {
		$undoBlocks = [];
		foreach($this->blocks as $block) {
			if($block instanceof Flowable) {
				$undoBlocks[] = $block;
				if($this->getMain()->getSettings()->get("Drop-Leafblower-Plants")) {
					$this->level->dropItem($block, Item::get($block->getId()));
				}
				$this->level->setBlock($block, Block::get(Block::AIR), false, false);
			}
		}
		$this->getMain()->getUndoStore()->saveUndo($undoBlocks, $this->player);
		return true;
	}
	
	public function getName(): string {
		return "Leaf Blower";
	}
	
	public function getPermission(): string {
		return "blocksniper.type.leafblower";
	}
}
