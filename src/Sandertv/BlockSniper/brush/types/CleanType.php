<?php

namespace Sandertv\BlockSniper\brush\types;

use pocketmine\block\Block;
use pocketmine\level\Level;
use pocketmine\math\Vector3;
use Sandertv\BlockSniper\brush\BaseType;
use Sandertv\BlockSniper\Loader;

class CleanType extends BaseType {
	
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
		$targetX = $this->center->x;
		$targetY = $this->center->y;
		$targetZ = $this->center->z;
		
		$minX = $targetX - $this->radius;
		$minY = $targetY - $this->radius;
		$minZ = $targetZ - $this->radius;
		$maxX = $targetX + $this->radius;
		$maxY = $targetY + $this->radius;
		$maxZ = $targetZ + $this->radius;
		
		$undoBlocks = [];
		
		for($x = $minX; $x <= $maxX; $x++) {
			for($y = $minY; $y <= $maxY; $y++) {
				for($z = $minZ; $z <= $maxZ; $z++) {
					$bId = $this->level->getBlock(new Vector3($x, $y, $z))->getId();
					$originBlock = $this->level->getBlock(new Vector3($x, $y, $z));
					if($bId !== 0 && $bId !== 1 && $bId !== 2 && $bId !== 3 && $bId !== 12 && $bId !== 13 && $bId !== 24) {
						if($originBlock->getId() !== Block::AIR) {
							$undoBlocks[] = $originBlock;
						}
						$this->level->setBlock(new Vector3($x, $y, $z), Block::get(Block::AIR), false, false);
					}
				}
			}
		}
		$this->getMain()->getUndoStore()->saveUndo($undoBlocks);
		return true;
	}
	
	public function getName(): string {
		return "Clean";
	}
	
	public function getPermission(): string {
		return "blocksniper.type.clean";
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
