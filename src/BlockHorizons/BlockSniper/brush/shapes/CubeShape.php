<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\brush\shapes;

use BlockHorizons\BlockSniper\brush\BaseShape;
use pocketmine\level\Level;
use pocketmine\level\Position;
use pocketmine\math\Vector3;
use pocketmine\Player;

class CubeShape extends BaseShape{

	const ID = self::SHAPE_CUBE;

	public function __construct(Player $player, Level $level, int $width, Position $center, bool $hollow = false){
		parent::__construct($player, $level, $center, $hollow);
		$this->width = $width;
	}

	/**
	 * @param bool $vectorOnly
	 *
	 * @return array
	 */
	public function getBlocksInside(bool $vectorOnly = false) : \Generator{
		[$targetX, $targetY, $targetZ] = $this->arrayVec($this->center);
		[$minX, $minY, $minZ, $maxX, $maxY, $maxZ] = $this->calculateBoundaryBlocks($targetX, $targetY, $targetZ, $this->width, $this->width);

		for($x = $minX; $x <= $maxX; $x++){
			for($z = $minZ; $z <= $maxZ; $z++){
				for($y = $minY; $y <= $maxY; $y++){
					if($this->hollow === true){
						if($x !== $maxX && $x !== $minX && $y !== $maxY && $y !== $minY && $z !== $maxZ && $z !== $minZ){
							continue;
						}
					}
					yield $vectorOnly ? new Vector3($x, $y, $z) : $this->getLevel()->getBlock(new Vector3($x, $y, $z));
				}
			}
		}
	}

	/**
	 * @return string
	 */
	public function getName() : string{
		return $this->hollow ? "Hollow Cube" : "Cube";
	}

	/**
	 * @return int
	 */
	public function getApproximateProcessedBlocks() : int{
		if($this->hollow){
			$blockCount = ($this->width * 2) ** 2 * 6;
		}else{
			$blockCount = ($this->width * 2) ** 3;
		}

		return (int) ceil($blockCount);
	}

	/**
	 * Returns the width of the cube.
	 *
	 * @return int
	 */
	public function getWidth() : int{
		return $this->width;
	}

	/**
	 * @return array
	 */
	public function getTouchedChunks() : array{
		$maxX = $this->center->x + $this->width;
		$minX = $this->center->x - $this->width;
		$maxZ = $this->center->z + $this->width;
		$minZ = $this->center->z - $this->width;

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
