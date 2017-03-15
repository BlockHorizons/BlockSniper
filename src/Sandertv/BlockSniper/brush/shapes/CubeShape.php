<?php

namespace Sandertv\BlockSniper\brush\shapes;

use pocketmine\level\Level;
use pocketmine\level\Position;
use pocketmine\math\Vector3;
use pocketmine\Player;
use Sandertv\BlockSniper\brush\BaseShape;
use Sandertv\BlockSniper\brush\Brush;
use Sandertv\BlockSniper\Loader;

class CubeShape extends BaseShape {
	
	public function __construct(Loader $main, Player $player, Level $level, int $width = null, Position $center = null, bool $hollow = false) {
		parent::__construct($main);
		$this->level = $level;
		$this->width = $width;
		$this->center = $center;
		$this->player = $player;
		$this->hollow = $hollow;
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
					if(Brush::getGravity($this->player) === true || Brush::getGravity($this->player) === 1) {
						$gravityY = ($this->level->getHighestBlockAt($x, $z) + 1) <= $maxY ? $this->level->getHighestBlockAt($x, $z) + 1 : $y;
					}
					$blocksInside[] = $this->getLevel()->getBlock(new Vector3($x, (isset($gravityY) ? $gravityY : $y), $z));
					unset($temporalY);
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
	
	public function getApproximateProcessedBlocks(): int {
		$blockCount = abs(($this->center->x - $this->radius) - ($this->center->x + $this->radius)) * abs(($this->center->z - $this->radius) - ($this->center->z + $this->radius)) * abs(($this->center->y - $this->radius) - ($this->center->y + $this->radius));
		return $blockCount;
	}
}
