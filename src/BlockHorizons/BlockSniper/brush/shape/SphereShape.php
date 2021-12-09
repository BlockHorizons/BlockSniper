<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\brush\shape;

use BlockHorizons\BlockSniper\brush\BrushProperties;
use BlockHorizons\BlockSniper\brush\Shape;
use Generator;
use pocketmine\math\AxisAlignedBB;
use pocketmine\math\Vector3;

class SphereShape extends Shape{

	public function getVectors() : Generator{
		$radiusX = ($this->selection->maxX - $this->selection->minX) / 2;
		$radiusXS = (int) $radiusX === 0 ? 1 : $radiusX ** 2;
		$radiusY = ($this->selection->maxY - $this->selection->minY) / 2;
		$radiusYS = (int) $radiusY === 0 ? 1 : $radiusY ** 2;
		$radiusZ = ($this->selection->maxZ - $this->selection->minZ) / 2;
		$radiusZS = (int) $radiusZ === 0 ? 1 : $radiusZ ** 2;

		$avgRadius = ($radiusX + $radiusY + $radiusZ) / 3;
		$rSquared = $avgRadius ** 2;

		$centerX = $this->selection->minX + $radiusX;
		$centerY = $this->selection->minY + $radiusY;
		$centerZ = $this->selection->minZ + $radiusZ;

		for($x = $this->selection->maxX; $x >= $this->selection->minX; $x--){
			$xs = ($x - $centerX) ** 2 / $radiusXS;
			for($y = $this->selection->maxY; $y >= $this->selection->minY; $y--){
				if($y > 255 || $y < 0){
					continue;
				}
				$ys = ($y - $centerY) ** 2 / $radiusYS;
				for($z = $this->selection->maxZ; $z >= $this->selection->minZ; $z--){
					$zs = ($z - $centerZ) ** 2 / $radiusZS;
					if($xs + $ys + $zs <= 1.0){
						if($this->hollow){
							if(($xs * $radiusXS + $ys * $radiusYS + $zs * $radiusZS) < $rSquared - 3 - $avgRadius * 2){
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
		$radiusXS = (int) $radiusX === 0 ? 1 : $radiusX ** 2;
		$radiusY = ($this->selection->maxY - $this->selection->minY) / 2;
		$radiusYS = (int) $radiusY === 0 ? 1 : $radiusY ** 2;
		$radiusZ = ($this->selection->maxZ - $this->selection->minZ) / 2;
		$radiusZS = (int) $radiusZ === 0 ? 1 : $radiusZ ** 2;

		$centerX = $this->selection->minX + $radiusX;
		$centerY = $this->selection->minY + $radiusY;
		$centerZ = $this->selection->minZ + $radiusZ;

		for($x = $this->selection->maxX; $x >= $this->selection->minX; $x--){
			$xs = ($x - $centerX) ** 2 / $radiusXS;
			for($y = $this->selection->maxY; $y >= $this->selection->minY; $y--){
				$ys = ($y - $centerY) ** 2 / $radiusYS;
				for($z = $this->selection->maxZ; $z >= $this->selection->minZ; $z--){
					$zs = ($z - $centerZ) ** 2 / $radiusZS;
					if($xs + $ys + $zs <= 1.0){
						if($this->hollow){
							if($xs + $ys + $zs < 0.85){
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
		return $this->hollow ? "Hollow Sphere" : "Sphere";
	}

	/**
	 * @param Vector3         $center
	 * @param BrushProperties $properties
	 */
	public function buildSelection(Vector3 $center, BrushProperties $properties) : AxisAlignedBB{
		[$maxX, $maxY, $maxZ, $minX, $minY, $minZ] = [
			$center->x + $properties->size, $center->y + $properties->size, $center->z + $properties->size,
			$center->x - $properties->size, $center->y - $properties->size, $center->z - $properties->size
		];
		return new AxisAlignedBB($minX, $minY, $minZ, $maxX, $maxY, $maxZ);
	}
}
