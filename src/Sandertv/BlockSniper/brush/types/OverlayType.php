<?php

namespace Sandertv\BlockSniper\brush\types;

use pocketmine\block\Block;
use pocketmine\item\Item;
use pocketmine\level\Level;
use pocketmine\math\Math;
use pocketmine\math\Vector3;
use Sandertv\BlockSniper\brush\BaseType;

class OverlayType extends BaseType {
	
	/** @noinspection PhpMissingParentConstructorInspection */
	public function __construct(Level $level, float $radius = 0, Vector3 $center = null, array $blocks = []) {
		$this->level = $level;
		$this->center = $center;
		$this->blocks = $blocks;
		$this->radius = $radius;
		
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
		$targetX = $this->center->x;
		$targetY = $this->center->y;
		$targetZ = $this->center->z;
		
		$minX = $targetX - $this->radius;
		$minY = $targetY - $this->radius;
		$minZ = $targetZ - $this->radius;
		$maxX = $targetX + $this->radius;
		$maxY = $targetY + $this->radius;
		$maxZ = $targetZ + $this->radius;
		
		for($x = $minX; $x <= $maxX; $x++) {
			for($y = $minY; $y <= $maxY; $y++) {
				for($z = $minZ; $z <= $maxZ; $z++) {
					$block = $this->level->getBlock(new Vector3($x, $y, $z));
					if($block->getId() !== Item::AIR) {
						$directions = [
							$block->getSide(Block::SIDE_DOWN),
							$block->getSide(Block::SIDE_UP),
							$block->getSide(Block::SIDE_NORTH),
							$block->getSide(Block::SIDE_SOUTH),
							$block->getSide(Block::SIDE_WEST),
							$block->getSide(Block::SIDE_EAST)
						];
						$valid = true;
						foreach($this->blocks as $possibleBlock) {
							if(is_numeric($possibleBlock)) {
								if($block->getId() === $possibleBlock) {
									$valid = false;
								}
							} else {
								if($block->getId() === Item::fromString($possibleBlock)->getId()) {
									$valid = false;
								}
							}
						}
						foreach($directions as $direction) {
							if($this->level->getBlock($direction)->getId() === Item::AIR && $valid) {
								$randomName = $this->blocks[array_rand($this->blocks)];
								$randomBlock = is_numeric($randomName) ? Item::get($randomName)->getBlock() : Item::fromString($randomName)->getBlock();
								if(($randomBlock !== 0 || strtolower($randomName) === "air") && $block->getId() !== $randomBlock->getId()) {
									$this->level->setBlock($direction, $randomBlock, false, false);
								}
							}
						}
					}
				}
			}
		}
		if($randomBlock->getId() === Block::AIR && strtolower($randomName) !== "air") {
			return false;
		}
		return true;
	}
	
	public function getName(): string {
		return "Overlay";
	}
	
	public function getPermission(): string {
		return "blocksniper.type.overlay";
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
