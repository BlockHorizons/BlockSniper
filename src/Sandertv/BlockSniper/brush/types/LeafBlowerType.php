<?php

namespace Sandertv\BlockSniper\brush\types;

use pocketmine\block\Block;
use pocketmine\block\Flowable;
use pocketmine\item\Item;
use pocketmine\level\Level;
use pocketmine\Player;
use Sandertv\BlockSniper\brush\BaseType;
use Sandertv\BlockSniper\Loader;

class LeafBlowerType extends BaseType {
	
	public $level;
	public $player;
	public $blocks;
	
	public function __construct(Loader $main, Player $player, Level $level, array $blocks) {
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
				$this->level->dropItem($block, Item::get($block->getId()));
				$this->level->setBlock($block, Block::get(Block::AIR), false, false);
			}
		}
		$this->getMain()->getUndoStore()->saveUndo($undoBlocks);
		return true;
	}
	
	public function getName(): string {
		return "Leaf Blower";
	}
	
	public function getPermission(): string {
		return "blocksniper.type.leafblower";
	}
	
	public function getApproximateBlocks(): int {
		// TODO
	}
	
	public function getLevel(): Level {
		return $this->level;
	}
}
