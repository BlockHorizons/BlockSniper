<?php

namespace Sandertv\BlockSniper\brush\shapes;

use pocketmine\level\Level;
use pocketmine\level\Position;
use pocketmine\Player;
use Sandertv\BlockSniper\brush\BaseShape;
use Sandertv\BlockSniper\brush\BrushManager;

class PyramidShape extends BaseShape {
	
	public function __construct(Player $player, Level $level, int $width = null, Position $center = null, bool $hollow = false) {
		$this->level = $level;
		$this->width = $width;
		$this->height = BrushManager::get($player)->getHeight();
		$this->center = $center;
		$this->player = $player;
		$this->hollow = $hollow;
	}
	
	/**
	 * @return array
	 */
	public function getBlocksInside(): array {
		//TODO: Implement Pyramids
	}
	
	public function getName(): string {
		return "Pyramid";
	}
	
	public function getPermission(): string {
		return "blocksniper.shape.pyramid";
	}
	
	public function getApproximateProcessedBlocks(): int {
		//TODO: Implement Pyramids
	}
}
