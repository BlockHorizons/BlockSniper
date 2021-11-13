<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\brush\shape;

use BlockHorizons\BlockSniper\brush\BrushProperties;
use BlockHorizons\BlockSniper\brush\Shape;
use Generator;
use pocketmine\math\AxisAlignedBB;
use pocketmine\math\Vector3;

class CylinderShape extends Shape{

	public function getVectors() : Generator{
		$radiusX = ($this->selection->maxX - $this->selection->minX) / 2;
		$radiusXS = (int) $radiusX === 0 ? 10 : $radiusX ** 2;
		$radiusZ = ($this->selection->maxZ - $this->selection->minZ) / 2;
		$radiusZS = (int) $radiusZ === 0 ? 10 : $radiusZ ** 2;

		$avgRadius = ($radiusX + $radiusZ) / 2;
		$rSquared = $avgRadius ** 2;

		$centerX = $this->selection->minX + $radiusX;
		$centerZ = $this->selection->minZ + $radiusZ;

		for($x = $this->selection->minX; $x <= $this->selection->maxX; $x++){
			$xs = ($x - $centerX) ** 2 / $radiusXS;
			for($z = $this->selection->minZ; $z <= $this->selection->maxZ; $z++){
				$zs = ($z - $centerZ) ** 2 / $radiusZS;
				for($y = $this->selection->minY; $y <= $this->selection->maxY; $y++){
					if($y > 255 || $y < 0){
						continue;
					}
					if($xs + $zs <= 1.0){
						if($this->hollow){
							if(($xs * $radiusXS + $zs * $radiusZS) < $rSquared - 3 - $avgRadius * 2 && $y !== $this->selection->minY && $y !== $this->selection->maxY){
								continue;
							}
						}
						yield new Vector3((int) $x, (int) $y, (int) $z);
					}
				}
			}
		}
	}

	/**
	 * @return int
	 */
	public function getBlockCount() : int{
		$i = 0;
		$radiusX = ($this->selection->maxX - $this->selection->minX) / 2;
		$radiusXS = (int) $radiusX === 0 ? 10 : $radiusX ** 2;
		$radiusZ = ($this->selection->maxZ - $this->selection->minZ) / 2;
		$radiusZS = (int) $radiusZ === 0 ? 10 : $radiusZ ** 2;

		$centerX = $this->selection->minX + $radiusX;
		$centerZ = $this->selection->minZ + $radiusZ;

		for($x = $this->selection->minX; $x <= $this->selection->maxX; $x++){
			$xs = ($x - $centerX) ** 2 / $radiusXS;
			for($z = $this->selection->minZ; $z <= $this->selection->maxZ; $z++){
				$zs = ($z - $centerZ) ** 2 / $radiusZS;
				for($y = $this->selection->minY; $y <= $this->selection->maxY; $y++){
					if($y >= 255 || $y <= 0){
						continue;
					}
					if($xs + $zs <= 1.0){
						if($this->hollow){
							if($xs + $zs < 0.85 && $y !== $this->selection->minY && $y !== $this->selection->maxY){
								continue;
							}
						}
						++$i;
					}
				}
			}
		}

		return $i;
	}

	/**
	 * @return string
	 */
	public function getName() : string{
		return $this->hollow ? "Hollow Standing Cylinder" : "Standing Cylinder";
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
