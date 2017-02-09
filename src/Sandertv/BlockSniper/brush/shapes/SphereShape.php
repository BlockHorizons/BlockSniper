<?php

namespace Sandertv\BlockSniper\brush\shapes;

use pocketmine\level\Level;
use pocketmine\math\Math;
use pocketmine\math\Vector3;
use pocketmine\level\Position;
use pocketmine\Player;
use Sandertv\BlockSniper\brush\BaseShape;
use Sandertv\BlockSniper\brush\Brush;
use Sandertv\BlockSniper\Loader;

class SphereShape extends BaseShape {
	
	public $level;
	public $radius;
	public $center;
	public $player;
	
	public function __construct(Loader $main, Player $player, Level $level, float $radius = null, Position $center = null) {
		parent::__construct($main);
		$this->level = $level;
		$this->radius = $radius;
		$this->center = $center;
		$this->player = $player;
	}
	
	/**
	 * @return array
	 */
	public function getBlocksInside(): array {
		$trueSphere = Brush::getPerfect($this->player);
		$radiusSquared = pow($this->radius + ($trueSphere ? 0 : -0.5), 2) + ($trueSphere ? 0.5 : 0);
		
		$targetX = $this->center->x;
		$targetY = $this->center->y;
		$targetZ = $this->center->z;
		
		$minX = Math::floorFloat($targetX - $this->radius);
		$maxX = Math::floorFloat($targetX + $this->radius) + 1;
		$minY = max(Math::floorFloat($targetY - $this->radius), 0);
		$maxY = min(Math::floorFloat($targetY + $this->radius) + 1, BaseShape::MAX_WORLD_HEIGHT);
		$minZ = Math::floorFloat($targetZ - $this->radius);
		$maxZ = Math::floorFloat($targetZ + $this->radius) + 1;
		
		$blocksInside = [];
		
		for($x = $maxX; $x >= $minX; $x--) {
			$xs = ($targetX - $x) * ($targetX - $x);
			for($y = $maxY; $y >= $minY; $y--) {
				$ys = ($targetY - $y) * ($targetY - $y);
				for($z = $maxZ; $z >= $minZ; $z--) {
					$zs = ($targetZ - $z) * ($targetZ - $z);
					if($xs + $ys + $zs < $radiusSquared) {
						if(Brush::getGravity($this->player) === true || Brush::getGravity($this->player) === 1) {
							$gravityY = ($this->level->getHighestBlockAt($x, $z) + 1) <= $maxY ? $this->level->getHighestBlockAt($x, $z) + 1 : $y;
						}
						$blocksInside[] = $this->getLevel()->getBlock(new Vector3($x, (isset($gravityY) ? $gravityY : $y), $z));
						unset($gravityY);
					}
				}
			}
		}
		return $blocksInside;
	}
	
	public function getLevel(): Level {
		return $this->level;
	}
	
	public function getName(): string {
		return "Sphere";
	}
	
	public function getPermission(): string {
		return "blocksniper.shape.sphere";
	}
	
	public function getApproximateBlocks(): int {
		// TODO
	}
	
	public function getRadius(): float {
		return $this->radius;
	}
	
	public function setRadius(float $radius) {
		$this->radius = $radius;
	}
	
	public function getCenter(): Position {
		return $this->center;
	}
	
	public function setCenter(Position $center) {
		$this->center = $center;
	}
	
	public function getBlocks(): array {
		return $this->blocks;
	}
	
	public function setBlocks(array $blocks) {
		$this->blocks = $blocks;
	}
}
