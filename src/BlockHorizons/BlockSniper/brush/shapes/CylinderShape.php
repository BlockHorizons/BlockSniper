<?php

namespace BlockHorizons\BlockSniper\brush\shapes;

use BlockHorizons\BlockSniper\brush\BaseShape;
use BlockHorizons\BlockSniper\brush\BrushManager;
use pocketmine\level\Level;
use pocketmine\level\Position;
use pocketmine\math\Vector3;
use pocketmine\Player;

class CylinderShape extends BaseShape {

	private $trueCircle;

	public function __construct(Player $player, Level $level, int $radius = null, Position $center = null, bool $hollow = false, bool $cloneShape = false) {
		parent::__construct($player, $level, $center, $hollow);
		$this->radius = $radius;
		$this->height = BrushManager::get($player)->getHeight();
		if($cloneShape) {
			$this->center->y += $this->height;
		}
		$this->trueCircle = BrushManager::get($player)->getPerfect();
	}

	/**
	 * @param bool $partially
	 * @param int  $blocksPerTick
	 *
	 * @return array
	 */
	public function getBlocksInside(bool $partially = false, int $blocksPerTick = 100): array {
		$radiusSquared = pow($this->radius + ($this->trueCircle ? 0 : -0.5), 2) + ($this->trueCircle ? 0.5 : 0);
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
		$i = 0;
		$skipBlocks = 1;
		
		for($x = $minX; $x <= $maxX; $x++) {
			for($z = $minZ; $z <= $maxZ; $z++) {
				for($y = $minY; $y <= $maxY; $y++) {
					if(pow($targetX - $x, 2) + pow($targetZ - $z, 2) <= $radiusSquared) {
						if($this->hollow === true) {
							if($y !== $maxY && $y !== $minY && (pow($targetX - $x, 2) + pow($targetZ - $z, 2)) < $radiusSquared - 3 - $this->radius / 0.5) {
								continue;
							}
						}
						if($partially) {
							for($skip = $skipBlocks; $skip <= $this->getProcessedBlocks(); $skip++) {
								$skipBlocks++;
								continue 2;
							}
							if($i > $blocksPerTick) {
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
		return $this->hollow ? "Hollow Standing Cylinder" : "Standing Cylinder";
	}

	/**
	 * @return int
	 */
	public function getApproximateProcessedBlocks(): int {
		if($this->hollow) {
			$blockCount = (M_PI * $this->radius * $this->radius * 2) + (2 * M_PI * $this->radius * $this->height * 2);
		} else {
			$blockCount = $this->radius * $this->radius * M_PI * $this->height;
		}

		return ceil($blockCount);
	}
}
