<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\brush\shapes;

use BlockHorizons\BlockSniper\brush\Brush;
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
