<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\brush\shapes;

use BlockHorizons\BlockSniper\brush\BaseShape;
use BlockHorizons\BlockSniper\brush\Brush;
use pocketmine\math\AxisAlignedBB;
use pocketmine\math\Vector3;

class CuboidShape extends BaseShape{

	const ID = self::SHAPE_CUBOID;

	/**
	 * @param bool $vectorOnly
	 *
	 * @return \Generator
	 */
	public function getBlocksInside(bool $vectorOnly = false) : \Generator{
		for($x = $this->minX; $x <= $this->maxX; $x++){
			for($y = $this->minY; $y <= $this->maxY; $y++){
				for($z = $this->minZ; $z <= $this->maxZ; $z++){
					if($this->hollow){
						if($x !== $this->maxX && $x !== $this->minX && $y !== $this->maxY && $y !== $this->minY && $z !== $this->maxZ && $z !== $this->minZ){
							continue;
						}
					}
					yield $vectorOnly ? new Vector3((int) $x, (int) $y, (int) $z) : $this->getLevel()->getBlock(new Vector3($x, $y, $z));
				}
			}
		}
	}

	/**
	 * @return int
	 */
	public function getBlockCount() : int {
		$i = 0;
		for($x = $this->minX; $x <= $this->maxX; $x++){
			for($y = $this->minY; $y <= $this->maxY; $y++){
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
	 * @param Vector3       $center
	 * @param Brush         $brush
	 * @param AxisAlignedBB $bb
	 */
	public function buildSelection(Vector3 $center, Brush $brush, AxisAlignedBB $bb) : void{
		[$bb->maxX, $bb->maxY, $bb->maxZ, $bb->minX, $bb->minY, $bb->minZ] = [
			$center->x + $brush->size, $center->y + $brush->height, $center->z + $brush->size,
			$center->x - $brush->size, $center->y - $brush->height, $center->z - $brush->size
		];
	}
}
