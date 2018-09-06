<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\brush\shapes;

use BlockHorizons\BlockSniper\brush\BaseShape;
use BlockHorizons\BlockSniper\sessions\SessionManager;
use pocketmine\level\Level;
use pocketmine\level\Position;
use pocketmine\math\Vector3;
use pocketmine\Player;

class PyramidShape extends BaseShape{

	public function __construct(Player $player, Level $level, int $width, Position $center, bool $hollow = false){
		parent::__construct($player, $level, $center, $hollow);
		$this->width = $width;
		$this->height = SessionManager::getPlayerSession($player)->getBrush()->height;
	}

	/**
	 * @param bool $vectorOnly
	 *
	 * @return array
	 */
	public function getBlocksInside(bool $vectorOnly = false) : \Generator{
		[$targetX, $targetY, $targetZ] = $this->arrayVec($this->center);

		$minX = $targetX - $this->width;
		$minY = $targetY - $this->height;
		$minZ = $targetZ - $this->width;
		$maxX = $targetX + $this->width;
		$maxY = $targetY + $this->height;
		$maxZ = $targetZ + $this->width;

		for($x = $minX; $x <= $maxX; $x++){
			for($y = $minY; $y <= $maxY; $y++){
				for($z = $minZ; $z <= $maxZ; $z++){
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

	public function getName() : string{
		return $this->hollow ? "Hollow Pyramid" : "Pyramid";
	}

	/**
	 * @return int
	 */
	public function getApproximateProcessedBlocks() : int{
		return 1 / 3 * $this->width * $this->width * $this->height;
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
