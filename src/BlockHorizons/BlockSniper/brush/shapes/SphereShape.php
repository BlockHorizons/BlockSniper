<?php

namespace BlockHorizons\BlockSniper\brush\shapes;

use BlockHorizons\BlockSniper\brush\BaseShape;
use BlockHorizons\BlockSniper\brush\BrushManager;
use pocketmine\level\Level;
use pocketmine\level\Position;
use pocketmine\math\Vector3;
use pocketmine\Player;

class SphereShape extends BaseShape {
	
	public function __construct(Player $player, Level $level, int $radius = null, Position $center = null, bool $hollow = false) {
		parent::__construct($player, $level, $center, $hollow);
		$this->radius = $radius;
	}
	
	/**
	 * @return array
	 */
	public function getBlocksInside(): array {
		$trueSphere = BrushManager::get($this->player)->getPerfect();
		$radiusSquared = pow($this->radius + ($trueSphere ? 0 : -0.5), 2) + ($trueSphere ? 0.5 : 0);
		
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
		
		for($x = $maxX; $x >= $minX; $x--) {
			$xs = ($targetX - $x) * ($targetX - $x);
			for($y = $maxY; $y >= $minY; $y--) {
				$ys = ($targetY - $y) * ($targetY - $y);
				for($z = $maxZ; $z >= $minZ; $z--) {
					$zs = ($targetZ - $z) * ($targetZ - $z);
					if($xs + $ys + $zs < $radiusSquared) {
						if($this->hollow === true) {
							if($y !== $maxY && $y !== $minY && ($xs + $ys + $zs) < $radiusSquared - 3 - $this->radius / 0.5) {
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
		return $this->hollow ? "Hollow Sphere" : "Sphere";
	}
	
	public function getApproximateProcessedBlocks(): int {
		$blockCount = round(4 / 3 * M_PI * pow($this->radius, 3));
		return $blockCount;
	}
}
