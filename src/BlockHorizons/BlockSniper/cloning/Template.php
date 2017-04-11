<?php

namespace BlockHorizons\BlockSniper\cloning;

use BlockHorizons\BlockSniper\Loader;
use pocketmine\item\Item;
use pocketmine\level\Level;
use pocketmine\level\Position;
use pocketmine\math\Vector3;

class Template extends BaseClone {
	
	public function __construct(Loader $loader, Level $level, string $name, Position $center = null, float $radius = null, int $height = 0) {
		parent::__construct($loader);
		$this->loader = $loader;
		$this->level = $level;
		$this->center = $center;
		$this->radius = $radius;
		$this->height = $height;
		$this->name = $name;
	}
	
	public function getName(): string {
		return "Template";
	}
	
	public function getPermission(): string {
		return "blocksniper.cloning.template";
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
		
		$templateBlocks = [];
		
		for($x = $minX; $x <= $maxX; $x++) {
			for($z = $minZ; $z <= $maxZ; $z++) {
				for($y = $minY; $y <= $maxY; $y++) {
					if(pow($targetX - $x, 2) + pow($targetZ - $z, 2) <= $radiusSquared) {
						$originBlock = $this->level->getBlock(new Vector3($x, $y, $z));
						if($originBlock->getId() !== Item::AIR) {
							$templateBlocks[] = $originBlock;
						}
					}
				}
			}
		}
		$this->getLoader()->getCloneStore()->saveTemplate($this->name, $templateBlocks, $this->center);
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