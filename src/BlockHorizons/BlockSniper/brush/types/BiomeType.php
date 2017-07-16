<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockSniper\brush\types;

use BlockHorizons\BlockSniper\brush\BaseType;
use BlockHorizons\BlockSniper\brush\BrushManager;
use pocketmine\level\Level;
use pocketmine\Player;

class BiomeType extends BaseType {
	
	/*
	 * Changes the biome within the brush radius.
	 */
	public function __construct(Player $player, Level $level, array $blocks) {
		parent::__construct($player, $level, $blocks);
		$this->biome = BrushManager::get($player)->getBiomeId();
	}

	/**
	 * @return array
	 */
	public function fillShape(): array {
		foreach($this->blocks as $block) {
			$this->level->setBiomeId($block->x, $block->z, $this->biome);
		}
		return [];
	}
	
	public function getName(): string {
		return "Biome";
	}
}
