<?php

namespace Sandertv\BlockSniper\brush;

use Sandertv\BlockSniper\Loader;

abstract class BaseShape {
	
	const MAX_WORLD_HEIGHT = 256;
	const MIN_WORLD_HEIGHT = 0;
	
	const SHAPE_CUBE = 0;
	const SHAPE_SPHERE = 1;
	const SHAPE_CYLINDER = 2;
	const SHAPE_CUBOID = 3;
	
	public function __construct(Loader $main) {
		$this->main = $main;
	}
	
	public abstract function getName(): string;
	
	public abstract function getPermission(): string;
	
	public abstract function getBlocksInside(): array;
	
	public function getMain(): Loader {
		return $this->main;
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
}
