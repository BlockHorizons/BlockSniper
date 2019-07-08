<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\brush\shape;

use BlockHorizons\BlockSniper\brush\BrushProperties;
use pocketmine\math\AxisAlignedBB;
use pocketmine\math\Vector3;

class EllipsoidShape extends SphereShape{

	/**
	 * @return string
	 */
	public function getName() : string{
		return $this->hollow ? "Hollow Ellipsoid" : "Ellipsoid";
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

	/**
	 * @return bool
	 */
	public function usesThreeLengths() : bool{
		return true;
	}
}