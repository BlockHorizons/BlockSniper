<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockSniper\brush\shapes;

use BlockHorizons\BlockSniper\brush\BaseShape;
use BlockHorizons\BlockSniper\brush\BrushManager;
use pocketmine\level\Level;
use pocketmine\level\Position;
use pocketmine\math\Vector3;
use pocketmine\Player;

class CylinderShape extends BaseShape {

	/** @var int */
	protected $radius = 0;
	/** @var bool */
	private $trueCircle = false;
	/** @var int */
	protected $height = 0;

	public function __construct(Player $player, Level $level, int $radius = null, Position $center = null, bool $hollow = false, bool $cloneShape = false) {
		parent::__construct($player, $level, $center, $hollow);
		$this->radius = $radius;
		$this->height = BrushManager::get($player)->getHeight();
		if($cloneShape) {
			$this->center[1] += $this->height;
		}
		$this->trueCircle = BrushManager::get($player)->getPerfect();
	}

	/**
	 * @param bool $vectorOnly
	 *
	 * @return array
	 */
	public function getBlocksInside(bool $vectorOnly = false): array {
		$radiusSquared = pow($this->radius + ($this->trueCircle ? 0 : -0.5), 2) + ($this->trueCircle ? 0.5 : 0);
		$targetX = $this->center[0];
		$targetY = $this->center[1];
		$targetZ = $this->center[2];

		$minX = $targetX - $this->radius;
		$minZ = $targetZ - $this->radius;
		$minY = $targetY - $this->height;
		$maxX = $targetX + $this->radius;
		$maxZ = $targetZ + $this->radius;
		$maxY = $targetY + $this->height;

		$blocksInside = [];

		for($x = $minX; $x <= $maxX; $x++) {
			for($z = $minZ; $z <= $maxZ; $z++) {
				for($y = $minY; $y <= $maxY; $y++) {
					if(pow($targetX - $x, 2) + pow($targetZ - $z, 2) <= $radiusSquared) {
						if($this->hollow === true) {
							if($y !== $maxY && $y !== $minY && (pow($targetX - $x, 2) + pow($targetZ - $z, 2)) < $radiusSquared - 3 - $this->radius / 0.5) {
								continue;
							}
						}
						$blocksInside[] = $vectorOnly ? new Vector3($x, $y, $z) : $this->getLevel()->getBlock(new Vector3($x, $y, $z));
					}
				}
			}
		}
		return $blocksInside;
	}

	/**
	 * @return string
	 */
	public function getName(): string {
		return $this->hollow ? "Hollow Standing Cylinder" : "Standing Cylinder";
	}

	/**
	 * @return int
	 */
	public function getApproximateProcessedBlocks(): int {
		if($this->hollow) {
			$blockCount = (M_PI * $this->radius * $this->radius * 2) + (2 * M_PI * $this->radius * $this->height * 2);
		} else {
			$blockCount = $this->radius * $this->radius * M_PI * $this->height;
		}

		return (int) ceil($blockCount);
	}

	/**
	 * Returns the height of the shape.
	 *
	 * @return int
	 */
	public function getHeight(): int {
		return $this->height;
	}

	/**
	 * Returns the radius of the cylinder.
	 *
	 * @return int
	 */
	public function getRadius(): int {
		return $this->radius;
	}

	/**
	 * @return array
	 */
	public function getTouchedChunks(): array {
		$maxX = $this->center[0] + $this->radius;
		$minX = $this->center[0] - $this->radius;
		$maxZ = $this->center[2] + $this->radius;
		$minZ = $this->center[2] - $this->radius;

		$touchedChunks = [];
		for($x = $minX; $x <= $maxX + 16; $x += 16) {
			for($z = $minZ; $z <= $maxZ + 16; $z += 16) {
				$chunk = $this->getLevel()->getChunk($x >> 4, $z >> 4, true);
				$touchedChunks[] = $chunk->fastSerialize();
			}
		}
		return $touchedChunks;
	}
}
