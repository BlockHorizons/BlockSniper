<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\brush\shapes;

use BlockHorizons\BlockSniper\brush\BaseShape;
use BlockHorizons\BlockSniper\sessions\SessionManager;
use pocketmine\level\Level;
use pocketmine\level\Position;
use pocketmine\math\Vector3;
use pocketmine\Player;

class CylinderShape extends BaseShape{

	const ID = self::SHAPE_CYLINDER;

	public function __construct(Player $player, Level $level, int $radius, Position $center, bool $hollow = false){
		parent::__construct($player, $level, $center, $hollow);
		$this->radius = $radius;
		$this->height = SessionManager::getPlayerSession($player)->getBrush()->height;
	}

	/**
	 * @param bool $vectorOnly
	 *
	 * @return array
	 */
	public function getBlocksInside(bool $vectorOnly = false) : \Generator{
		$radiusSquared = $this->radius ** 2 + 0.5;
		[$targetX, $targetY, $targetZ] = $this->arrayVec($this->center);
		[$minX, $minY, $minZ, $maxX, $maxY, $maxZ] = $this->calculateBoundaryBlocks($targetX, $targetY, $targetZ, $this->radius, $this->height);

		for($x = $minX; $x <= $maxX; $x++){
			for($z = $minZ; $z <= $maxZ; $z++){
				for($y = $minY; $y <= $maxY; $y++){
					if(($targetX - $x) ** 2 + ($targetZ - $z) ** 2 <= $radiusSquared){
						if($this->hollow === true){
							if($y !== $maxY && $y !== $minY && (($targetX - $x) ** 2 + ($targetZ - $z) ** 2) < $radiusSquared - 3 - $this->radius / 0.5){
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
		return $this->hollow ? "Hollow Standing Cylinder" : "Standing Cylinder";
	}

	/**
	 * @return int
	 */
	public function getApproximateProcessedBlocks() : int{
		if($this->hollow){
			$blockCount = (M_PI * $this->radius * $this->radius * 2) + (2 * M_PI * $this->radius * $this->height * 2);
		}else{
			$blockCount = $this->radius * $this->radius * M_PI * $this->height;
		}

		return (int) ceil($blockCount);
	}

	/**
	 * Returns the height of the shape.
	 *
	 * @return int
	 */
	public function getHeight() : int{
		return $this->height;
	}

	/**
	 * Returns the radius of the cylinder.
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
