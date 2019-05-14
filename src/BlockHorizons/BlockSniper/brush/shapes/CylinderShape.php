<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\brush\shapes;

use BlockHorizons\BlockSniper\brush\BrushProperties;
use BlockHorizons\BlockSniper\brush\Shape;
use pocketmine\math\AxisAlignedBB;
use pocketmine\math\Vector3;

class CylinderShape extends Shape{

	public const ID = self::SHAPE_CYLINDER;

	/**
	 * @return \Generator
	 */
	public function getVectors() : \Generator{
		$radiusX = ($this->maxX - $this->minX) / 2;
		$radiusXS = (int) $radiusX === 0 ? 10 : $radiusX ** 2;
		$radiusZ = ($this->maxZ - $this->minZ) / 2;
		$radiusZS = (int) $radiusZ === 0 ? 10 : $radiusZ ** 2;

		$avgRadius = ($radiusX + $radiusZ) / 2;
		$rSquared = $avgRadius ** 2;

		$centerX = $this->minX + $radiusX;
		$centerZ = $this->minZ + $radiusZ;

		for($x = $this->minX; $x <= $this->maxX; $x++){
			$xs = ($x - $centerX) ** 2 / $radiusXS;
			for($z = $this->minZ; $z <= $this->maxZ; $z++){
				$zs = ($z - $centerZ) ** 2 / $radiusZS;
				for($y = $this->minY; $y <= $this->maxY; $y++){
					if($y > 255 || $y < 0){
						continue;
					}
					if($xs + $zs <= 1.0){
						if($this->hollow){
							if(($xs * $radiusXS + $zs * $radiusZS) < $rSquared - 3 - $avgRadius * 2 && $y !== $this->minY && $y !== $this->maxY){
								continue;
							}
						}
						new Vector3((int) $x, (int) $y, (int) $z);
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
		$radiusX = ($this->maxX - $this->minX) / 2;
		$radiusXS = (int) $radiusX === 0 ? 10 : $radiusX ** 2;
		$radiusZ = ($this->maxZ - $this->minZ) / 2;
		$radiusZS = (int) $radiusZ === 0 ? 10 : $radiusZ ** 2;

		$centerX = $this->minX + $radiusX;
		$centerZ = $this->minZ + $radiusZ;

		for($x = $this->minX; $x <= $this->maxX; $x++){
			$xs = ($x - $centerX) ** 2 / $radiusXS;
			for($z = $this->minZ; $z <= $this->maxZ; $z++){
				$zs = ($z - $centerZ) ** 2 / $radiusZS;
				for($y = $this->minY; $y <= $this->maxY; $y++){
					if($y >= 255 || $y <= 0){
						continue;
					}
					if($xs + $zs <= 1.0){
						if($this->hollow){
							if($xs + $zs < 0.85 && $y !== $this->minY && $y !== $this->maxY){
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
	 * @param BrushProperties $brush
	 * @param AxisAlignedBB   $bb
	 */
	public function buildSelection(Vector3 $center, BrushProperties $brush, AxisAlignedBB $bb) : void{
		[$bb->maxX, $bb->maxY, $bb->maxZ, $bb->minX, $bb->minY, $bb->minZ] = [
			$center->x + $brush->width, $center->y + $brush->height, $center->z + $brush->length,
			$center->x - $brush->width, $center->y - $brush->height, $center->z - $brush->length
		];
	}
}
