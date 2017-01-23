<?php

namespace Sandertv\BlockSniper\brush\shapes;

use pocketmine\block\Block;
use pocketmine\item\Item;
use pocketmine\level\Level;
use pocketmine\math\Vector3;
use Sandertv\BlockSniper\brush\BaseShape;
use Sandertv\BlockSniper\Loader;

class CylinderStandingShape extends BaseShape {
	
	public function __construct(Loader $main, Level $level, float $radius = null, int $height = null, Vector3 $center = null) {
		parent::__construct($main);
		$this->level = $level;
		$this->radius = $radius;
		$this->height = $height;
		$this->center = $center;
		
		if(!isset($center)) {
			$this->center = new Vector3(0, 0, 0);
		}
	}
	
	/**
	 * @return array
	 */
	public function getBlocksInside(): array {
		$radiusSquared = pow($this->radius, 2);
		$targetX = $this->center->x;
		$targetY = $this->center->y;
		$targetZ = $this->center->z;
		
		$minX = $targetX - $this->radius;
		$minZ = $targetZ - $this->radius;
		$minY = $targetY - $this->height;
		$maxX = $targetX + $this->radius;
		$maxZ = $targetZ + $this->radius;
		$maxY = $targetY + $this->height;

		$blocksInside = [];
		
		for($x = $minX; $x <= $maxX; $x++) {
			for($z = $minZ; $z <= $maxZ; $z++) {
				for($y = $minY; $y <= $maxY; $y++) {
					if(pow($targetX - $x, 2) + pow($targetZ - $z, 2) <= $radiusSquared) {
						$blocksInside[] = $this->getLevel()->getBlock(new Vector3($x, $y, $z));
					}
				}
			}
		}
		return $blocksInside;
	}
	
	public function getName(): string {
		return "Standing Cylinder";
	}
	
	public function getPermission(): string {
		return "blocksniper.shape.cylinderstanding";
	}
	
	public function getApproximateBlocks(): int {
		// TODO
	}
	
	public function getCenter(): Vector3 {
		return $this->center;
	}
	
	public function setCenter(Vector3 $center) {
		$this->center = $center;
	}
	
	public function getLevel(): Level {
		return $this->level;
	}
}
