<?php

namespace Sandertv\BlockSniper\brush\shapes;

use pocketmine\block\Block;
use pocketmine\item\Item;
use pocketmine\level\Level;
use pocketmine\math\Math;
use pocketmine\math\Vector3;
use Sandertv\BlockSniper\brush\BaseShape;
use Sandertv\BlockSniper\Loader;

class SphereShape extends BaseShape {
	
	public function __construct(Loader $main, Level $level, float $radius = null, Vector3 $center = null, array $blocks = []) {
		parent::__construct($main);
		$this->level = $level;
		$this->radius = $radius;
		$this->center = $center;
		$this->blocks = $blocks;
		
		if(!isset($center)) {
			$this->center = new Vector3(0, 0, 0);
		}
		if(!isset($blocks)) {
			$this->blocks = ["Air"];
		}
	}
	
	/**
	 * @return bool
	 */
	public function fillShape(): bool {
		$radiusSquared = pow($this->radius - 0.5, 2);
		
		$targetX = $this->center->x;
		$targetY = $this->center->y;
		$targetZ = $this->center->z;
		
		$minX = Math::floorFloat($targetX - $this->radius);
		$maxX = Math::floorFloat($targetX + $this->radius) + 1;
		$minY = max(Math::floorFloat($targetY - $this->radius), 0);
		$maxY = min(Math::floorFloat($targetY + $this->radius) + 1, BaseShape::MAX_WORLD_HEIGHT);
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
						$randomName = $this->blocks[array_rand($this->blocks)];
						$randomBlock = is_numeric($randomName) ? Item::get($randomName)->getBlock() : Item::fromString($randomName)->getBlock();
						$originBlock = $this->level->getBlock(new Vector3($x, $y, $z));
						if($randomBlock !== 0 || strtolower($randomName) === "air") {
							if($originBlock->getId() !== $randomBlock->getId()) {
								$undoBlocks[] = $originBlock;
							}
							$this->level->setBlock(new Vector3($x, $y, $z), $randomBlock, false, false);
						}
					}
				}
			}
		}
		if($randomBlock->getId() === Block::AIR && strtolower($randomName) !== "air") {
			return false;
		}
		$this->getMain()->getUndoStore()->saveUndo($undoBlocks);
		return true;
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
