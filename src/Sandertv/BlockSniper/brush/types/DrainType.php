<?php

namespace Sandertv\BlockSniper\brush\types;

use pocketmine\block\Block;
use pocketmine\item\Item;
use pocketmine\level\Level;
use pocketmine\math\Math;
use pocketmine\math\Vector3;
use Sandertv\BlockSniper\brush\BaseType;
use Sandertv\BlockSniper\Loader;

class DrainType extends BaseType {
	
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
		
		$minX = Math::floorFloat($targetX - $this->radius);
		$maxX = Math::floorFloat($targetX + $this->radius) + 1;
		$minY = max(Math::floorFloat($targetY - $this->radius), 0);
		$maxY = min(Math::floorFloat($targetY + $this->radius) + 1, BaseType::MAX_WORLD_HEIGHT);
		$minZ = Math::floorFloat($targetZ - $this->radius);
		$maxZ = Math::floorFloat($targetZ + $this->radius) + 1;
		
		$undoBlocks = [];
		
		for($x = $maxX; $x >= $minX; $x--) {
			$xs = ($targetX - $x) * ($targetX - $x);
			for($y = $maxY; $y >= $minY; $y--) {
				$ys = ($targetY - $y) * ($targetY - $y);
				for($z = $maxZ; $z >= $minZ; $z--) {
					$zs = ($targetZ - $z) * ($targetZ - $z);
					if($xs + $ys + $zs < $radiusSquared) {
						$blockId = $this->level->getBlock(new Vector3($x, $y, $z))->getId();
						$originBlock = $this->level->getBlock(new Vector3($x, $y, $z));
						if($blockId === Item::LAVA || $blockId === Item::WATER || $blockId === Item::STILL_LAVA || $blockId === Item::STILL_WATER) {
							if($originBlock->getId() !== Block::AIR) {
								$undoBlocks[] = $originBlock;
							}
							$this->level->setBlock(new Vector3($x, $y, $z), Block::get(Block::AIR), false, false);
						}
					}
				}
			}
		}
		$this->getMain()->getUndoStore()->saveUndo($undoBlocks);
		return true;
	}
	
	public function getName(): string {
		return "Drain";
	}
	
	public function getPermission(): string {
		return "blocksniper.type.drain";
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