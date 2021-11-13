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
	 * @param BrushProperties $properties
	 */
	public function buildSelection(Vector3 $center, BrushProperties $properties) : AxisAlignedBB{
		[$maxX, $maxY, $maxZ, $minX, $minY, $minZ] = [
			$center->x + $properties->width, $center->y + $properties->height, $center->z + $properties->length,
			$center->x - $properties->width, $center->y - $properties->height, $center->z - $properties->length
		];
		return new AxisAlignedBB($minX, $minY, $minZ, $maxX, $maxY, $maxZ);
	}

	/**
	 * @return bool
	 */
	public function usesThreeLengths() : bool{
		return true;
	}
}