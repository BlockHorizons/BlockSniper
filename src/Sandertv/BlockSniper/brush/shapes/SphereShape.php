<?php

namespace Sandertv\BlockSniper\brush\shapes;

use Sandertv\BlockSniper\brush\Brush;
use pocketmine\level\Level;
use pocketmine\math\Math;
use pocketmine\math\Vector3;
use Sandertv\BlockSniper\brush\BaseShape;
use Sandertv\BlockSniper\Loader;
use pocketmine\Player;

class SphereShape extends BaseShape {
	
	public $level;
	public $radius;
	public $center;
	public $player;
	
	public function __construct(Loader $main, Player $player, Level $level, float $radius = null, Vector3 $center = null) {
		parent::__construct($main);
		$this->level = $level;
		$this->radius = $radius;
		$this->center = $center;
		$this->player = $player;
		
		if(!isset($center)) {
			$this->center = new Vector3(0, 0, 0);
		}
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
						$blocksInside[] = $this->getLevel()->getBlock(new Vector3($x, $y, $z));
					}
				}
			}
		}
		return $blocksInside;
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
	
	public function getCenter(): Vector3 {
		return $this->center;
	}
	
	public function setCenter(Vector3 $center) {
		$this->center = $center;
	}
	
	public function getBlocks(): array {
		return $this->blocks;
	}
	
	public function setBlocks(array $blocks) {
		$this->blocks = $blocks;
	}
	
	public function getLevel(): Level {
		return $this->level;
	}
}
