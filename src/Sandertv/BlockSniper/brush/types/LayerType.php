<?php

namespace Sandertv\BlockSniper\brush\types;

use pocketmine\block\Block;
use pocketmine\item\Item;
use pocketmine\level\Level;
use pocketmine\math\Vector3;
use Sandertv\BlockSniper\brush\BaseType;

class LayerType extends BaseType {
	
	/** @noinspection PhpMissingParentConstructorInspection */
	public function __construct(Level $level, float $radius = null, Vector3 $center = null, array $blocks = []) {
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
		$radiusSquared = pow($this->radius, 2);
		$targetX = $this->center->x;
		$targetY = $this->center->y;
		$targetZ = $this->center->z;
		
		$minX = $targetX - $this->radius;
		$minZ = $targetZ - $this->radius;
		$maxX = $targetX + $this->radius;
		$maxZ = $targetZ + $this->radius;
		
		$valid = false;
		for($x = $minX; $x <= $maxX; $x++) {
			for($z = $minZ; $z <= $maxZ; $z++) {
				$randomName = $this->blocks[array_rand($this->blocks)];
				$randomBlock = is_numeric($randomName) ? Item::get($randomName)->getBlock() : Item::fromString($randomName)->getBlock();
				if(pow($targetX - $x, 2) + pow($targetZ - $z, 2) <= $radiusSquared) {
					if($randomBlock->getId() !== 0 || strtolower($randomName) === "air") {
						$this->level->setBlock(new Vector3($x, $targetY + 1, $z), $randomBlock, false, false);
						$valid = true;
					}
				}
			}
		}
		if($randomBlock === Block::AIR && strtolower($randomName) !== "air") {
			return false;
		}
		if($valid) {
			return true;
		}
		return false;
	}
	
	public function getName(): string {
		return "Layer";
	}
	
	public function getPermission(): string {
		return "blocksniper.type.layer";
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

