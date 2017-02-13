<?php

namespace Sandertv\BlockSniper\brush\shapes;

use pocketmine\level\Level;
use pocketmine\level\Position;
use pocketmine\math\Vector3;
use pocketmine\Player;
use Sandertv\BlockSniper\brush\BaseShape;
use Sandertv\BlockSniper\brush\Brush;
use Sandertv\BlockSniper\Loader;

class CuboidShape extends BaseShape {
	
	public $level;
	public $width;
	public $height;
	public $center;
	public $player;
	
	public function __construct(Loader $main, Player $player, Level $level, float $width = null, float $height = null, Position $center = null) {
		parent::__construct($main);
		$this->level = $level;
		$this->width = $width;
		$this->height = $height;
		$this->center = $center;
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
		$minY = $targetY - $this->height;
		$minZ = $targetZ - $this->width;
		$maxX = $targetX + $this->width;
		$maxY = $targetY + $this->height;
		$maxZ = $targetZ + $this->width;
		
		$blocksInside = [];
		
		for($x = $minX; $x <= $maxX; $x++) {
			for($y = $minY; $y <= $maxY; $y++) {
				for($z = $minZ; $z <= $maxZ; $z++) {
					if(Brush::getGravity($this->player) === true || Brush::getGravity($this->player) === 1) {
						$gravityY = ($this->level->getHighestBlockAt($x, $z) + 1) <= $maxY ? $this->level->getHighestBlockAt($x, $z) + 1 : $y;
					}
					$blocksInside[] = $this->getLevel()->getBlock(new Vector3($x, (isset($gravityY) ? $gravityY : $y), $z));
					unset($gravityY);
				}
			}
		}
		return $blocksInside;
	}
	
	public function getLevel(): Level {
		return $this->level;
	}
	
	public function getName(): string {
		return "Cuboid";
	}
	
	public function getPermission(): string {
		return "blocksniper.shape.cuboid";
	}
	
	public function getApproximateBlocks(): int {
		// TODO
	}
	
	public function getCenter(): Position {
		return $this->center;
	}
	
	public function setCenter(Position $center) {
		$this->center = $center;
	}
}
