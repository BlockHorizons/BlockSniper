<?php

namespace BlockHorizons\BlockSniper\brush\shapes;

use BlockHorizons\BlockSniper\brush\BaseShape;
use BlockHorizons\BlockSniper\brush\BrushManager;
use pocketmine\level\Level;
use pocketmine\level\Position;
use pocketmine\math\Vector3;
use pocketmine\Player;

class CylinderShape extends BaseShape {
	
	public function __construct(Player $player, Level $level, int $radius = null, Position $center = null, bool $hollow = false) {
		parent::__construct($player, $level, $center, $hollow);
		$this->radius = $radius;
		$this->height = BrushManager::get($player)->getHeight();
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
						if($this->hollow === true) {
							if($y !== $maxY && $y !== $minY && (pow($targetX - $x, 2) + pow($targetZ - $z, 2)) < $radiusSquared - 3 - $this->radius / 0.5) {
								continue;
							}
						}
						$blocksInside[] = $this->getLevel()->getBlock(new Vector3($x, $y, $z));
					}
				}
			}
		}
		return $blocksInside;
	}
	
	public function getName(): string {
		return $this->hollow ? "Hollow Standing Cylinder" : "Standing Cylinder";
	}
	
	public function getApproximateProcessedBlocks(): int {
		$blockCount = round($this->radius * $this->radius * M_PI * $this->height);
		return $blockCount;
	}
}
