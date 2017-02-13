<?php

namespace Sandertv\BlockSniper\brush\shapes;

use pocketmine\level\Level;
use pocketmine\level\Position;
use pocketmine\math\Vector3;
use pocketmine\Player;
use Sandertv\BlockSniper\brush\BaseShape;
use Sandertv\BlockSniper\brush\Brush;
use Sandertv\BlockSniper\Loader;

class CubeShape extends BaseShape {
	
	public $level;
	public $radius;
	public $center;
	public $player;
	
	public function __construct(Loader $main, Player $player, Level $level, float $radius = null, Position $center = null) {
		parent::__construct($main);
		$this->level = $level;
		$this->radius = $radius;
		$this->center = $center;
		$this->player = $player;
	}
	
	/**
	 * @return array
	 */
	public function getBlocksInside(): array {
		$targetX = $this->center->x;
		$targetY = $this->center->y;
		$targetZ = $this->center->z;
		
		$minX = $targetX - $this->radius;
		$minZ = $targetZ - $this->radius;
		$minY = $targetY - $this->radius;
		$maxX = $targetX + $this->radius;
		$maxZ = $targetZ + $this->radius;
		$maxY = $targetY + $this->radius;
		$blocksInside = [];
		
		for($x = $minX; $x <= $maxX; $x++) {
			for($z = $minZ; $z <= $maxZ; $z++) {
				for($y = $minY; $y <= $maxY; $y++) {
					if(Brush::getGravity($this->player) === true || Brush::getGravity($this->player) === 1) {
						$gravityY = ($this->level->getHighestBlockAt($x, $z) + 1) <= $maxY ? $this->level->getHighestBlockAt($x, $z) + 1 : $y;
					}
					$blocksInside[] = $this->getLevel()->getBlock(new Vector3($x, (isset($gravityY) ? $gravityY : $y), $z));
					unset($temporalY);
				}
			}
		}
		return $blocksInside;
	}
	
	public function getLevel(): Level {
		return $this->level;
	}
	
	public function getName(): string {
		return "Cube";
	}
	
	public function getPermission(): string {
		return "blocksniper.shape.cube";
	}
	
	public function getApproximateBlocks(): int {
		// TODO
	}
	
	public function getCenter(): Position {
		return $this->center;
	}
	
	public function setCenter(Position $center) {
		$this->center = $center;
	}
}
