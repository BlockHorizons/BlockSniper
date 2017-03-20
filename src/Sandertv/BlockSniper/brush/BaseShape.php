<?php

namespace Sandertv\BlockSniper\brush;

use pocketmine\level\Level;
use pocketmine\level\Position;
use pocketmine\Player;
use Sandertv\BlockSniper\brush\shapes\CubeShape;
use Sandertv\BlockSniper\brush\shapes\CuboidShape;
use Sandertv\BlockSniper\brush\shapes\CylinderShape;
use Sandertv\BlockSniper\brush\shapes\SphereShape;
use Sandertv\BlockSniper\Loader;

abstract class BaseShape {
	
	const MAX_WORLD_HEIGHT = 256;
	const MIN_WORLD_HEIGHT = 0;
	
	const SHAPE_SPHERE = 0;
	const SHAPE_CUBE = 1;
	const SHAPE_CYLINDER = 2;
	const SHAPE_CUBOID = 3;
	
	public $level;
	public $player;
	public $main;
	
	protected $width;
	protected $radius;
	protected $center;
	protected $hollow;
	protected $height;
	
	public function __construct(Loader $main) {
		$this->main = $main;
	}
	
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
		define(('Sandertv\BlockSniper\brush\BaseShape\\' . $shapeConst), $number);
		return true;
	}
	
	/**
	 * Returns the name of the shape.
	 *
	 * @return string
	 */
	public abstract function getName(): string;
	
	/**
	 * Returns the permission node connected to the shape.
	 *
	 * @return string
	 */
	public abstract function getPermission(): string;
	
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
	
	public function getMain(): Loader {
		return $this->main;
	}
	
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
}
