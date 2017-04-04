<?php

namespace Sandertv\BlockSniper\brush\types;

use pocketmine\level\Level;
use pocketmine\Player;
use Sandertv\BlockSniper\brush\BaseType;
use Sandertv\BlockSniper\brush\BrushManager;
use Sandertv\BlockSniper\Loader;
use Sandertv\BlockSniper\undo\UndoStorer;

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
	
	public function getPermission(): string {
		return "blocksniper.type.biome";
	}
}
