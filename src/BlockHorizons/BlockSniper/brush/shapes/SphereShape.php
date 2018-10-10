<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\brush\shapes;

use BlockHorizons\BlockSniper\brush\BaseShape;
use BlockHorizons\BlockSniper\brush\Brush;
use pocketmine\math\AxisAlignedBB;
use pocketmine\math\Vector3;

class SphereShape extends BaseShape{

	const ID = self::SHAPE_SPHERE;

	/**
	 * @param bool $vectorOnly
	 *
	 * @return \Generator
	 */
	public function getBlocksInside(bool $vectorOnly = false) : \Generator{
		$radiusX = ($this->maxX - $this->minX) / 2;
		$radiusY = ($this->maxY - $this->minY) / 2;
		$radiusZ = ($this->maxZ - $this->minZ) / 2;

		$centerX = $this->minX + $radiusX;
		$centerY = $this->minY + $radiusY;
		$centerZ = $this->minZ + $radiusZ;

		for($x = $this->maxX; $x >= $this->minX; $x--){
			$xs = ($x - $centerX) ** 2 / $radiusX ** 2;
			for($y = $this->maxY; $y >= $this->minY; $y--){
				$ys = ($y - $centerY) ** 2 / $radiusY ** 2;
				for($z = $this->maxZ; $z >= $this->minZ; $z--){
					$zs = ($z - $centerZ) ** 2 / $radiusZ ** 2;
					if($xs + $ys + $zs <= 1.0){
						if($this->hollow){
							if($xs + $ys + $zs < 0.85){
								continue;
							}
						}
						yield $vectorOnly ? new Vector3($x, $y, $z) : $this->getLevel()->getBlock(new Vector3($x, $y, $z));
					}
				}
			}
		}
	}

	/**
	 * @return int
	 */
	public function getBlockCount() : int {
		$i = 0;
		$radiusX = ($this->maxX - $this->minX) / 2;
		$radiusY = ($this->maxY - $this->minY) / 2;
		$radiusZ = ($this->maxZ - $this->minZ) / 2;

		$centerX = $this->minX + $radiusX;
		$centerY = $this->minY + $radiusY;
		$centerZ = $this->minZ + $radiusZ;

		for($x = $this->maxX; $x >= $this->minX; $x--){
			$xs = ($x - $centerX) ** 2 / $radiusX ** 2;
			for($y = $this->maxY; $y >= $this->minY; $y--){
				$ys = ($y - $centerY) ** 2 / $radiusY ** 2;
				for($z = $this->maxZ; $z >= $this->minZ; $z--){
					$zs = ($z - $centerZ) ** 2 / $radiusZ ** 2;
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
	 * @param Vector3       $center
	 * @param Brush         $brush
	 * @param AxisAlignedBB $bb
	 */
	public function buildSelection(Vector3 $center, Brush $brush, AxisAlignedBB $bb) : void{
		[$bb->maxX, $bb->maxY, $bb->maxZ, $bb->minX, $bb->minY, $bb->minZ] = [
			$center->x + $brush->size, $center->y + $brush->size, $center->z + $brush->size,
			$center->x - $brush->size, $center->y - $brush->size, $center->z - $brush->size
		];
	}
}
