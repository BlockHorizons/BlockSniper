<?php

namespace Sandertv\BlockSniper\brush\types;

use pocketmine\level\Level;
use pocketmine\Player;
use Sandertv\BlockSniper\brush\BaseType;
use Sandertv\BlockSniper\brush\Brush;
use Sandertv\BlockSniper\Loader;

class BiomeType extends BaseType {
	
	public function __construct(Loader $main, Player $player, Level $level, array $blocks) {
		parent::__construct($main);
		$this->level = $level;
		$this->blocks = $blocks;
		$this->player = $player;
		$this->biome = Brush::getBiomeId($this->player);
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
