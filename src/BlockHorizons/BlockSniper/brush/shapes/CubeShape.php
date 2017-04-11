<?php

namespace BlockHorizons\BlockSniper\brush\shapes;

use BlockHorizons\BlockSniper\brush\BaseShape;
use pocketmine\level\Level;
use pocketmine\level\Position;
use pocketmine\math\Vector3;
use pocketmine\Player;

class CubeShape extends BaseShape {
	
	public function __construct(Player $player, Level $level, int $width = null, Position $center = null, bool $hollow = false) {
		parent::__construct($player, $level, $center, $hollow);
		$this->width = $width;
		$this->player = $player;
	}
	
	/**
	 * @return array
	 */
	public function getBlocksInside(): array {
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
		
		for($x = $minX; $x <= $maxX; $x++) {
			for($z = $minZ; $z <= $maxZ; $z++) {
				for($y = $minY; $y <= $maxY; $y++) {
					if($this->hollow === true) {
						if($x !== $maxX && $x !== $minX && $y !== $maxY && $y !== $minY && $z !== $maxZ && $z !== $minZ) {
							continue;
						}
					}
					$blocksInside[] = $this->getLevel()->getBlock(new Vector3($x, $y, $z));
				}
			}
		}
		return $blocksInside;
	}
	
	public function getName(): string {
		return $this->hollow ? "Hollow Cube" : "Cube";
	}
	
	public function getApproximateProcessedBlocks(): int {
		$blockCount = abs(($this->center->x - $this->radius) - ($this->center->x + $this->radius)) * abs(($this->center->z - $this->radius) - ($this->center->z + $this->radius)) * abs(($this->center->y - $this->radius) - ($this->center->y + $this->radius));
		return $blockCount;
	}
}
