<?php

namespace Sandertv\BlockSniper\brush\types;

use pocketmine\level\Level;
use pocketmine\math\Vector3;
use pocketmine\Player;
use Sandertv\BlockSniper\brush\BaseType;
use Sandertv\BlockSniper\brush\Brush;
use Sandertv\BlockSniper\Loader;

class BiomeType extends BaseType {
	
	public $level;
	public $blocks;
	public $player;
	
	public function __construct(Loader $main, Player $player, Level $level, array $blocks) {
		parent::__construct($main);
		$this->level = $level;
		$this->blocks = $blocks;
		
		$this->player = $player;
	}
	
	/**
	 * @return bool
	 */
	public function fillShape(): bool {
		foreach($this->blocks as $block) {
			$this->level->setBiomeId($block->x, $block->z, Brush::getBiomeIdFromString($this->player));
		}
		return true;
	}
	
	public function getName(): string {
		return "Biome";
	}
	
	public function getPermission(): string {
		return "blocksniper.type.biome";
	}
	
	public function getApproximateBlocks(): int {
		// TODO
	}
	
	public function getLevel(): Level {
		return $this->level;
	}
}
