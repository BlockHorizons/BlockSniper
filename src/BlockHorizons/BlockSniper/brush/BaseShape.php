<?php

namespace BlockHorizons\BlockSniper\brush;

use BlockHorizons\BlockSniper\brush\shapes\CubeShape;
use BlockHorizons\BlockSniper\brush\shapes\CuboidShape;
use BlockHorizons\BlockSniper\brush\shapes\CylinderShape;
use BlockHorizons\BlockSniper\brush\shapes\SphereShape;
use pocketmine\level\Level;
use pocketmine\level\Position;
use pocketmine\Player;

abstract class BaseShape {
	
	const MAX_WORLD_HEIGHT = 256;
	const MIN_WORLD_HEIGHT = 0;
	
	const SHAPE_SPHERE = 0;
	const SHAPE_CUBE = 1;
	const SHAPE_CYLINDER = 2;
	const SHAPE_CUBOID = 3;

	public $player;

	protected $level;
	protected $width;
	protected $radius;
	protected $center;
	protected $hollow;
	protected $height;

	/**
	 * @param string $shape
	 *
	 * @return bool
	 */
	public static function isShape(string $shape): bool {
		$shapeConst = strtoupper("shape_" . $shape);
		if(defined("self::$shapeConst")) {
			return true;
		}
		return false;
	}
	
	/**
	 * Registers a new Shape. Example:
	 * Triangle, 4
	 *
	 * Defines the shape as a constant making it able to be used.
	 *
	 *
	 * @param string $shape
	 * @param int    $number
	 *
	 * @return bool
	 */
	public static function registerShape(string $shape, int $number): bool {
		$shapeConst = strtoupper("shape_" . str_replace("_", "", $shape));
		if(defined("self::$shapeConst")) {
			return false;
		}
		define(('BlockHorizons\BlockSniper\brush\BaseShape\\' . $shapeConst), $number);
		return true;
	}

	/**
	 * @param Player   $player
	 * @param Level    $level
	 * @param Position $center
	 * @param bool     $hollow
	 */
	public function __construct(Player $player, Level $level, Position $center, bool $hollow) {
		$this->player = $player;
		$this->level = $level;
		$this->center = $center;
		$this->hollow = $hollow;
	}

	/**
	 * Returns the name of the shape.
	 *
	 * @return string
	 */
	public abstract function getName(): string;

	/**
	 * Returns all blocks inside of the shape.
	 *
	 * @return array
	 */
	public abstract function getBlocksInside(): array;
	
	/**
	 * Returns the approximate amount of processed blocks in the shape. This may not be perfectly accurate.
	 *
	 * @return int
	 */
	public abstract function getApproximateProcessedBlocks(): int;
	
	/**
	 * Returns the level the shape is made in.
	 *
	 * @return Level
	 */
	public function getLevel(): Level {
		return $this->level;
	}
	
	/**
	 * Returns the player that made the shape.
	 *
	 * @return Player
	 */
	public function getPlayer(): Player {
		return $this->player;
	}
	
	/**
	 * Returns the width in case of a CubeShape or CuboidShape.
	 *
	 * @return float
	 */
	public function getWidth(): float {
		if($this instanceof CubeShape || $this instanceof CuboidShape) {
			return $this->width;
		}
		return null;
	}
	
	/**
	 * Returns the radius in case of a SphereShape or CylinderShape.
	 *
	 * @return float|null
	 */
	public function getRadius(): int {
		if($this instanceof SphereShape || $this instanceof CylinderShape) {
			return $this->radius;
		}
		return null;
	}
	
	/**
	 * Returns the center of the shape made, or the target block.
	 *
	 * @return Position
	 */
	public function getCenter(): Position {
		return $this->center;
	}
	
	/**
	 * Returns true if the shape is hollow, false if it is not.
	 *
	 * @return bool
	 * @deprecated
	 */
	public function getHollow(): bool {
		return $this->hollow;
	}
	
	/**
	 * Returns the height in case of a CylinderShape or CuboidShape.
	 *
	 * @return int|null
	 */
	public function getHeight(): int {
		if($this instanceof CylinderShape || $this instanceof CuboidShape) {
			return $this->height;
		}
		return null;
	}

	/**
	 * Returns true if the shape is hollow, false if it is not.
	 *
	 * @return bool
	 */
	public function isHollow(): bool {
		return $this->hollow;
	}

	/**
	 * Returns the permission required to use the shape.
	 *
	 * @return string
	 */
	public function getPermission(): string {
		return "blocksniper.shape." . str_replace("hollow", "", str_replace(" ", "_", strtolower($this->getName())));
	}
}
