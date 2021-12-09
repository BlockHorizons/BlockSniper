<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\brush\shape;

use BlockHorizons\BlockSniper\brush\BrushProperties;
use BlockHorizons\BlockSniper\brush\Shape;
use Generator;
use pocketmine\math\AxisAlignedBB;
use pocketmine\math\Vector3;

class CuboidShape extends Shape{

	public function getVectors() : Generator{
		for($x = $this->selection->minX; $x <= $this->selection->maxX; $x++){
			for($y = $this->selection->minY; $y <= $this->selection->maxY; $y++){
				if($y > 255 || $y < 0){
					continue;
				}
				for($z = $this->selection->minZ; $z <= $this->selection->maxZ; $z++){
					if($this->hollow){
						if($x !== $this->selection->maxX && $x !== $this->selection->minX && $y !== $this->selection->maxY && $y !== $this->selection->minY && $z !== $this->selection->maxZ && $z !== $this->selection->minZ){
							continue;
						}
					}
					yield new Vector3((int) $x, (int) $y, (int) $z);
				}
			}
		}
	}

	/**
	 * @return int
	 */
	public function getBlockCount() : int{
		$i = 0;
		for($x = $this->selection->minX; $x <= $this->selection->maxX; $x++){
			for($y = $this->selection->minY; $y <= $this->selection->maxY; $y++){
				if($y >= 255 || $y <= 0){
					continue;
				}
				for($z = $this->selection->minZ; $z <= $this->selection->maxZ; $z++){
					if($this->hollow){
						if($x !== $this->selection->maxX && $x !== $this->selection->minX && $y !== $this->selection->maxY && $y !== $this->selection->minY && $z !== $this->selection->maxZ && $z !== $this->selection->minZ){
							continue;
						}
					}
					++$i;
				}
			}
		}

		return $i;
	}

	/**
	 * @return string
	 */
	public function getName() : string{
		return $this->hollow ? "Hollow Cuboid" : "Cuboid";
	}

	/**
	 * @return bool
	 */
	public function usesThreeLengths() : bool{
		return true;
	}

	/**
	 * @param Vector3         $center
	 * @param BrushProperties $properties
	 */
	public function buildSelection(Vector3 $center, BrushProperties $properties) : AxisAlignedBB{
		[$maxX, $maxY, $maxZ, $minX, $minY, $minZ] = [
			$center->x + $properties->width, $center->y + $properties->height, $center->z + $properties->length,
			$center->x - $properties->width, $center->y - $properties->height, $center->z - $properties->length
		];
		return new AxisAlignedBB($minX, $minY, $minZ, $maxX, $maxY, $maxZ);
	}
}
