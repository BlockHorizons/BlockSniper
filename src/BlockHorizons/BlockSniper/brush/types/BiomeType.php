<?php

namespace BlockHorizons\BlockSniper\brush\types;

use BlockHorizons\BlockSniper\brush\BaseType;
use BlockHorizons\BlockSniper\brush\BrushManager;
use BlockHorizons\BlockSniper\undo\UndoStorer;
use pocketmine\level\Level;
use pocketmine\Player;

class BiomeType extends BaseType {
	
	/*
	 * Changes the biome within the brush radius.
	 */
	public function __construct(UndoStorer $undoStorer, Player $player, Level $level, array $blocks) {
		parent::__construct($undoStorer, $player, $level, $blocks);
		$this->biome = BrushManager::get($player)->getBiomeId();
	}
	
	/**
	 * @return bool
	 */
	public function fillShape(): bool {
		foreach($this->blocks as $block) {
			$this->level->setBiomeId($block->x, $block->z, $this->biome);
		}
		return true;
	}
	
	public function getName(): string {
		return "Biome";
	}
}
