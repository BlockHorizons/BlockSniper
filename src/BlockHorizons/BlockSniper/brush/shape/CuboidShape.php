<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\brush\shape;

use BlockHorizons\BlockSniper\brush\BrushProperties;
use BlockHorizons\BlockSniper\brush\Shape;
use Generator;
use pocketmine\math\AxisAlignedBB;
use pocketmine\math\Vector3;

class CuboidShape extends Shape{

	public const ID = self::SHAPE_CUBOID;

	/**
	 * @return Generator
	 */
	public function getVectors() : Generator{
		for($x = $this->minX; $x <= $this->maxX; $x++){
			for($y = $this->minY; $y <= $this->maxY; $y++){
				if($y > 255 || $y < 0){
					continue;
				}
				for($z = $this->minZ; $z <= $this->maxZ; $z++){
					if($this->hollow){
						if($x !== $this->maxX && $x !== $this->minX && $y !== $this->maxY && $y !== $this->minY && $z !== $this->maxZ && $z !== $this->minZ){
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
		for($x = $this->minX; $x <= $this->maxX; $x++){
			for($y = $this->minY; $y <= $this->maxY; $y++){
				if($y >= 255 || $y <= 0){
					continue;
				}
				for($z = $this->minZ; $z <= $this->maxZ; $z++){
					if($this->hollow){
						if($x !== $this->maxX && $x !== $this->minX && $y !== $this->maxY && $y !== $this->minY && $z !== $this->maxZ && $z !== $this->minZ){
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
