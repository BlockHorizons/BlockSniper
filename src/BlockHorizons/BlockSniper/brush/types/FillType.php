<?php

namespace BlockHorizons\BlockSniper\brush\types;

use BlockHorizons\BlockSniper\brush\BaseType;
use BlockHorizons\BlockSniper\brush\BrushManager;
use BlockHorizons\BlockSniper\undo\UndoStorer;
use pocketmine\level\Level;
use pocketmine\math\Vector3;
use pocketmine\Player;

class FillType extends BaseType {
	
	/*
	 * Places blocks on every location within the brush radius.
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
			$randomBlock = BrushManager::get($this->player)->getBlocks()[array_rand(BrushManager::get($this->player)->getBlocks())];
			$undoBlocks[] = $block;
			$this->level->setBlock(new Vector3($block->x, $block->y, $block->z), $randomBlock, false, false);
		}
		$this->getUndoStore()->saveUndo($undoBlocks, $this->player);
		return true;
	}
	
	public function getName(): string {
		return "Fill";
	}
}
