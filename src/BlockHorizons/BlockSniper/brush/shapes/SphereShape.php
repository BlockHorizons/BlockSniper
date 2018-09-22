<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\brush\shapes;

use BlockHorizons\BlockSniper\brush\BaseShape;
use BlockHorizons\BlockSniper\brush\Brush;
use pocketmine\level\Level;
use pocketmine\level\Position;
use pocketmine\math\AxisAlignedBB;
use pocketmine\math\Vector3;
use pocketmine\Player;

class SphereShape extends BaseShape{

	const ID = self::SHAPE_SPHERE;

	/**
	 * @param bool $vectorOnly
	 *
	 * @return \Generator
	 */
	public function getBlocksInside(bool $vectorOnly = false) : \Generator{
		$radius = ($this->maxX - $this->minX) / 2;
		$centerX = $this->minX + $radius;
		$centerY = $this->minY + $radius;
		$centerZ = $this->minZ + $radius;
		$radiusSquared = $radius ** 2 + 0.5;

		for($x = $this->maxX; $x >= $this->minX; $x--){
			$xs = ($centerX - $x) ** 2;
			for($y = $this->maxY; $y >= $this->minY; $y--){
				$ys = ($this->center->y - $y) ** 2;
				for($z = $this->maxZ; $z >= $this->minZ; $z--){
					$zs = ($this->center->z - $z) ** 2;
					if($xs + $ys + $zs < $radiusSquared){
						if($this->hollow === true){
							if(($xs + $ys + $zs) < $radiusSquared - 3 - $radius / 0.5){
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

	/**
	 * @return array
	 */
	public function getTouchedChunks() : array{
		$touchedChunks = [];
		for($x = $this->minX; $x <= $this->maxX + 16; $x += 16){
			for($z = $this->minZ; $z <= $this->maxZ + 16; $z += 16){
				$chunk = $this->getLevel()->getChunk($x >> 4, $z >> 4, false);
				if($chunk === null){
					continue;
				}
				$touchedChunks[Level::chunkHash($x >> 4, $z >> 4)] = $chunk->fastSerialize();
			}
		}

		return $touchedChunks;
	}
}
