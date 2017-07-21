<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockSniper\brush\shapes;

use BlockHorizons\BlockSniper\brush\BaseShape;
use pocketmine\level\Level;
use pocketmine\level\Position;
use pocketmine\math\Vector3;
use pocketmine\Player;

class CubeShape extends BaseShape {
	
	public function __construct(Player $player, Level $level, int $width = null, Position $center = null, bool $hollow = false, bool $cloneShape = false) {
		parent::__construct($player, $level, $center, $hollow);
		$this->width = $width;
		$this->player = $player;
		if($cloneShape) {
			$this->center->y += $this->width;
		}
	}

	/**
	 * @param bool $partially
	 * @param int  $blocksPerTick
	 *
	 * @return array
	 */
	public function getBlocksInside(bool $partially = false, int $blocksPerTick = 100): array {
		$targetX = $this->center->x;
		$targetY = $this->center->y;
		$targetZ = $this->center->z;
		
		$minX = $targetX - $this->width;
		$minZ = $targetZ - $this->width;
		$minY = $targetY - $this->width;
		$maxX = $targetX + $this->width;
		$maxZ = $targetZ + $this->width;
		$maxY = $targetY + $this->width;

		$blocksInside = [];
		$skipBlocks = 1;
		$i = 0;
		
		for($x = $minX; $x <= $maxX; $x++) {
			for($z = $minZ; $z <= $maxZ; $z++) {
				for($y = $minY; $y <= $maxY; $y++) {
					if($this->hollow === true) {
						if($x !== $maxX && $x !== $minX && $y !== $maxY && $y !== $minY && $z !== $maxZ && $z !== $minZ) {
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
		$this->partialBlockCount += $i;
		return $blocksInside;
	}

	/**
	 * @return string
	 */
	public function getName(): string {
		return $this->hollow ? "Hollow Cube" : "Cube";
	}

	/**
	 * @return int
	 */
	public function getApproximateProcessedBlocks(): int {
		if($this->hollow) {
			$blockCount = pow($this->width * 2, 2) * 6;
		} else {
			$blockCount = pow($this->width * 2, 3);
		}
		return ceil($blockCount);
	}
}
