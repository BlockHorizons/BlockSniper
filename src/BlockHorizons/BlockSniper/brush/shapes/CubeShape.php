<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\brush\shapes;

use BlockHorizons\BlockSniper\brush\BrushProperties;
use pocketmine\math\AxisAlignedBB;
use pocketmine\math\Vector3;

class CubeShape extends CuboidShape{

	public const ID = self::SHAPE_CUBE;

	/**
	 * @return string
	 */
	public function getName() : string{
		return $this->hollow ? "Hollow Cube" : "Cube";
	}

	/**
	 * @param Vector3         $center
	 * @param BrushProperties $brush
	 * @param AxisAlignedBB   $bb
	 */
	public function buildSelection(Vector3 $center, BrushProperties $brush, AxisAlignedBB $bb) : void{
		[$bb->maxX, $bb->maxY, $bb->maxZ, $bb->minX, $bb->minY, $bb->minZ] = [
			$center->x + $brush->size, $center->y + $brush->size, $center->z + $brush->size,
			$center->x - $brush->size, $center->y - $brush->size, $center->z - $brush->size
		];
	}

	/**
	 * @return bool
	 */
	public function usesThreeLengths() : bool{
		return false;
	}
}
