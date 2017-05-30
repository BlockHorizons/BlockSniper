<?php

namespace BlockHorizons\BlockSniper\brush\shapes;

use BlockHorizons\BlockSniper\brush\BaseShape;
use BlockHorizons\BlockSniper\brush\BrushManager;
use pocketmine\level\Level;
use pocketmine\level\Position;
use pocketmine\math\Vector3;
use pocketmine\Player;

class SphereShape extends BaseShape {
	
	public function __construct(Player $player, Level $level, int $radius = null, Position $center = null, bool $hollow = false, bool $cloneShape = false) {
		parent::__construct($player, $level, $center, $hollow);
		$this->radius = $radius;
		if($cloneShape) {
			$this->center->y += $this->radius;
		}
	}

	/**
	 * @param bool $partially
	 * @param int  $blocksPerTick
	 *
	 * @return array
	 */
	public function getBlocksInside(bool $partially = false, int $blocksPerTick = 100): array {
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
		$i = 0;
		$skipBlocks = 1;
		
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
						if($partially) {
							for($skip = $skipBlocks; $skip <= $this->getProcessedBlocks(); $skip++) {
								$skipBlocks++;
								continue 2;
							}
							if($i > $blocksPerTick) {
								$blocksInside[] = $this->getLevel()->getBlock(new Vector3($x, $y, $z));
								$this->partialBlocks = array_merge($this->partialBlocks, $blocksInside);
								break 3;
							}
							$i++;
						}
						$blocksInside[] = $this->getLevel()->getBlock(new Vector3($x, $y, $z));
					}
				}
			}
		}
		$this->partialBlockCount += $i;
		return $blocksInside;
	}

	/**
	 * @return string
	 */
	public function getName(): string {
		return $this->hollow ? "Hollow Sphere" : "Sphere";
	}

	/**
	 * @return int
	 */
	public function getApproximateProcessedBlocks(): int {
		if($this->hollow) {
			$blockCount = round(4 * M_PI * $this->radius);
		} else {
			$blockCount = round(4 / 3 * M_PI * pow($this->radius, 3));
		}

		return $blockCount;
	}
}
