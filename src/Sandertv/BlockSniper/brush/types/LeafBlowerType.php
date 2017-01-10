<?php

namespace Sandertv\BlockSniper\brush\types;

use pocketmine\block\Block;
use pocketmine\block\Flowable;
use pocketmine\item\Item;
use pocketmine\level\Level;
use pocketmine\math\Vector3;
use Sandertv\BlockSniper\brush\BaseType;
use Sandertv\BlockSniper\Loader;

class LeafBlowerType extends BaseType {
	
	public function __construct(Loader $main, Level $level, float $radius = null, Vector3 $center = null) {
		parent::__construct($main);
		$this->level = $level;
		$this->radius = $radius;
		$this->center = $center;
		
		if(!isset($center)) {
			$this->center = new Vector3(0, 0, 0);
		}
	}
	
	/**
	 * @return bool
	 */
	public function fillShape(): bool {
		$radiusSquared = pow($this->radius, 2);
		$targetX = $this->center->x;
		$targetY = $this->center->y;
		$targetZ = $this->center->z;
		
		$minX = $targetX - $this->radius;
		$minZ = $targetZ - $this->radius;
		$minY = $targetY - 2;
		$maxX = $targetX + $this->radius;
		$maxZ = $targetZ + $this->radius;
		$maxY = $targetY + 2;
		
		$valid = false;
		for($x = $minX; $x <= $maxX; $x++) {
			for($z = $minZ; $z <= $maxZ; $z++) {
				for($y = $minY; $y <= $maxY; $y++) {
					if(pow($targetX - $x, 2) + pow($targetZ - $z, 2) <= $radiusSquared) {
						if($this->level->getBlock(new Vector3($x, $y, $z)) instanceof Flowable) {
							$this->level->dropItem(new Vector3($x, $y, $z), Item::get($this->level->getBlock(new Vector3($x, $y, $z))->getId()));
							$this->level->setBlock(new Vector3($x, $y, $z), Block::get(Block::AIR), false, false);
							$valid = true;
						}
					}
				}
			}
		}
		if($valid) {
			return true;
		}
		return false;
	}
	
	public function getName(): string {
		return "Leaf blower";
	}
	
	public function getPermission(): string {
		return "blocksniper.type.leafblower";
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
	
	public function getLevel(): Level {
		return $this->level;
	}
}
