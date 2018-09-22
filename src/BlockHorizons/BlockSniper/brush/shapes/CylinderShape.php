<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\brush\shapes;

use BlockHorizons\BlockSniper\brush\BaseShape;
use BlockHorizons\BlockSniper\brush\Brush;
use BlockHorizons\BlockSniper\sessions\SessionManager;
use pocketmine\level\Level;
use pocketmine\level\Position;
use pocketmine\math\AxisAlignedBB;
use pocketmine\math\Vector3;
use pocketmine\Player;

class CylinderShape extends BaseShape{

	const ID = self::SHAPE_CYLINDER;

	/**
	 * @param bool $vectorOnly
	 *
	 * @return \Generator
	 */
	public function getBlocksInside(bool $vectorOnly = false) : \Generator{
		[$rX, $rZ] = [($this->maxX - $this->minX) / 2, ($this->maxZ - $this->minZ) / 2];
		$centerX = $this->minX + $rX;
		$centerZ = $this->minZ + $rZ;
		$radiusSquared = $rX * $rZ + 0.5;

		for($x = $this->minX; $x <= $this->maxX; $x++){
			for($z = $this->minZ; $z <= $this->maxZ; $z++){
				for($y = $this->minY; $y <= $this->maxY; $y++){
					[$xDSquared, $zDSquared] = [($centerX - $x) ** 2, ($centerZ - $z) ** 2];
					if($xDSquared + $zDSquared <= $radiusSquared){
						if($this->hollow === true){
							if($y !== $this->maxY && $y !== $this->minY && $xDSquared + $zDSquared < $radiusSquared - 3 - ($rX + $rZ)){
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
