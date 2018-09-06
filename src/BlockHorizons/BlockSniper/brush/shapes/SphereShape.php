<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\brush\shapes;

use BlockHorizons\BlockSniper\brush\BaseShape;
use pocketmine\level\Level;
use pocketmine\level\Position;
use pocketmine\math\Vector3;
use pocketmine\Player;

class SphereShape extends BaseShape{

	const ID = self::SHAPE_SPHERE;

	public function __construct(Player $player, Level $level, int $radius, Position $center, bool $hollow = false){
		parent::__construct($player, $level, $center, $hollow);
		$this->radius = $radius;
	}

	/**
	 * @param bool $vectorOnly
	 *
	 * @return \Generator
	 */
	public function getBlocksInside(bool $vectorOnly = false) : \Generator{
		$radiusSquared = $this->radius ** 2 + 0.5;
		[$targetX, $targetY, $targetZ] = $this->arrayVec($this->center);
		[$minX, $minY, $minZ, $maxX, $maxY, $maxZ] = $this->calculateBoundaryBlocks($targetX, $targetY, $targetZ, $this->radius, $this->radius);

		for($x = $maxX; $x >= $minX; $x--){
			$xs = ($targetX - $x) * ($targetX - $x);
			for($y = $maxY; $y >= $minY; $y--){
				$ys = ($targetY - $y) * ($targetY - $y);
				for($z = $maxZ; $z >= $minZ; $z--){
					$zs = ($targetZ - $z) * ($targetZ - $z);
					if($xs + $ys + $zs < $radiusSquared){
						if($this->hollow === true){
							if($y !== $maxY && $y !== $minY && ($xs + $ys + $zs) < $radiusSquared - 3 - $this->radius / 0.5){
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
	 * @return int
	 */
	public function getApproximateProcessedBlocks() : int{
		if($this->hollow){
			$blockCount = 4 * M_PI * $this->radius;
		}else{
			$blockCount = 4 / 3 * M_PI * $this->radius ** 3;
		}

		return (int) ceil($blockCount);
	}

	/**
	 * Returns the radius of the sphere.
	 *
	 * @return int
	 */
	public function getRadius() : int{
		return $this->radius;
	}

	/**
	 * @return array
	 */
	public function getTouchedChunks() : array{
		$maxX = $this->center->x + $this->radius;
		$minX = $this->center->x - $this->radius;
		$maxZ = $this->center->z + $this->radius;
		$minZ = $this->center->z - $this->radius;

		$touchedChunks = [];
		for($x = $minX; $x <= $maxX + 16; $x += 16){
			for($z = $minZ; $z <= $maxZ + 16; $z += 16){
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
