<?php

namespace Sandertv\BlockSniper\brush\shapes;

use pocketmine\level\Level;
use pocketmine\math\Vector3;
use pocketmine\level\Position;
use Sandertv\BlockSniper\brush\BaseShape;
use Sandertv\BlockSniper\Loader;

class CubeShape extends BaseShape {
	
	public $level;
	public $radius;
	public $center;
	
	public function __construct(Loader $main, Level $level, float $radius = null, Position $center = null) {
		parent::__construct($main);
		$this->level = $level;
		$this->radius = $radius;
		$this->center = $center;
	}
	
	/**
	 * @return array
	 */
	public function getBlocksInside(): array {
		$targetX = $this->center->x;
		$targetY = $this->center->y;
		$targetZ = $this->center->z;
		
		$minX = $targetX - $this->radius;
		$minY = $targetY - $this->radius;
		$minZ = $targetZ - $this->radius;
		$maxX = $targetX + $this->radius;
		$maxY = $targetY + $this->radius;
		$maxZ = $targetZ + $this->radius;
		
		$blocksInside = [];
		
		for($x = $minX; $x <= $maxX; $x++) {
			for($y = $minY; $y <= $maxY; $y++) {
				for($z = $minZ; $z <= $maxZ; $z++) {
					$blocksInside[] = $this->getLevel()->getBlock(new Vector3($x, $y, $z));
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
