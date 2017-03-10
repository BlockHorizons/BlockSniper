<?php

namespace Sandertv\BlockSniper\brush;

use Sandertv\BlockSniper\Loader;

abstract class BaseShape {
	
	const MAX_WORLD_HEIGHT = 256;
	const MIN_WORLD_HEIGHT = 0;
	
	const SHAPE_SPHERE = 0;
	const SHAPE_CUBE = 1;
	const SHAPE_CYLINDER = 2;
	const SHAPE_CUBOID = 3;
	
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
	
	public abstract function getName(): string;
	
	public abstract function getPermission(): string;
	
	public abstract function getBlocksInside(): array;
	
	public abstract function getApproximateProcessedBlocks(): int;
	
	public function getMain(): Loader {
		return $this->main;
	}
}
