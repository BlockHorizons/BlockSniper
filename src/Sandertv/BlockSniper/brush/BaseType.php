<?php

namespace Sandertv\BlockSniper\brush;

use Sandertv\BlockSniper\Loader;

abstract class BaseType {
	
	const MAX_WORLD_HEIGHT = 256;
	const MIN_WORLD_HEIGHT = 0;
	
	const TYPE_FILL = 0;
	const TYPE_OVERLAY = 1;
	const TYPE_LAYER = 2;
	const TYPE_REPLACE = 3;
	const TYPE_FLATTEN = 4;
	const TYPE_DRAIN = 5;
	const TYPE_LEAFBLOWER = 6;
	const TYPE_CLEAN = 7;
	const TYPE_BIOME = 8;
	const TYPE_CLEANENTITIES = 9;
	const TYPE_MELT = 10;
	const TYPE_EXPAND = 11;
	const TYPE_RAISE = 12;
	
	public $level;
	public $player;
	public $main;
	protected $biome;
	protected $blocks;
	protected $center;
	protected $obsolete;
	
	public function __construct(Loader $main) {
		$this->main = $main;
	}
	
	/**
	 * @param string $type
	 *
	 * @return bool
	 */
	public static function isType(string $type): bool {
		$typeConst = strtoupper("type_" . $type);
		if(defined("self::$typeConst")) {
			return true;
		}
		return false;
	}
	
	/**
	 * Registers a new Type. Example:
	 * Raise, 12
	 *
	 * Defines the type as a constant making it able to be used.
	 *
	 *
	 * @param string $type
	 * @param int    $number
	 *
	 * @return bool
	 */
	public static function registerType(string $type, int $number): bool {
		$typeConst = strtoupper("type_" . str_replace("_", "", $type));
		if(defined("self::$typeConst")) {
			return false;
		}
		define(('Sandertv\BlockSniper\brush\BaseType\\' . $typeConst), $number);
		return true;
	}
	
	public abstract function getName(): string;
	
	public abstract function getPermission(): string;
	
	public abstract function fillShape(): bool;
	
	public function getMain(): Loader {
		return $this->main;
	}
}
