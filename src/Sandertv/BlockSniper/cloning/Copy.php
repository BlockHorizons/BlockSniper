<?php

namespace Sandertv\BlockSniper\cloning;

use Sandertv\BlockSniper\Loader;
use pocketmine\level\Level;
use pocketmine\math\Vector3;
use pocketmine\item\Item;

class Copy extends BaseClone {
	
	public function __construct(Loader $owner, Level $level, Vector3 $center = null, float $radius = null, int $height = 0) {
		$this->owner = $owner;
		$this->level = $level;
		$this->center = $center;
		$this->radius = $radius;
		$this->height = $height;
	}
	
	public function getName(): string {
		return "Copy";
	}
	
	public function getPermission(): string {
		return "blocksniper.cloning.copy";
	}
	
	public function saveClone() {
		$radiusSquared = pow($this->radius, 2);
		$targetX = $this->center->x;
		$targetY = $this->center->y;
		$targetZ = $this->center->z;
		
		$minX = $targetX - $this->radius;
		$minZ = $targetZ - $this->radius;
		$minY = $targetY;
		$maxX = $targetX + $this->radius;
		$maxZ = $targetZ + $this->radius;
		$maxY = $targetY + $this->height;
		
		$copyBlocks = [];
		
		for($x = $minX; $x <= $maxX; $x++) {
			for($z = $minZ; $z <= $maxZ; $z++) {
				for($y = $minY; $y <= $maxY; $y++) {
					if(pow($targetX - $x, 2) + pow($targetZ - $z, 2) <= $radiusSquared) {
						$originBlock = $this->level->getBlock(new Vector3($x, $y, $z));
						if($originBlock->getId() !== Item::AIR) {
							$copyBlocks[] = $originBlock;
						}
					}
				}
			}
		}
		$this->getOwner()->getCloneStore()->saveCopy($copyBlocks);
		$this->getOwner()->getCloneStore()->setOriginalCenter($this->center);
		return true;
	}
	
	public function getLevel(): Level {
		return $this->level;
	}
	
	public function getRadius(): float {
		return $this->radius;
	}
	
	public function getHeight(): int {
		return $this->height;
	}
	
	public function getCenter(): Vector3 {
		return $this->center;
	}
}