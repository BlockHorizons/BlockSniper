<?php

namespace Sandertv\BlockSniper\brush;

use pocketmine\level\Level;
use pocketmine\level\Position;
use pocketmine\Player;
use Sandertv\BlockSniper\brush\types\BiomeType;
use Sandertv\BlockSniper\brush\types\FlattenallType;
use Sandertv\BlockSniper\brush\types\FlattenType;
use Sandertv\BlockSniper\brush\types\LayerType;
use Sandertv\BlockSniper\brush\types\ReplaceType;
use Sandertv\BlockSniper\Loader;

abstract class BaseType {
	
	const MAX_WORLD_HEIGHT = 256;
	const MIN_WORLD_HEIGHT = 0;
	
	const TYPE_FILL = 0;
	const TYPE_OVERLAY = 1;
	const TYPE_LAYER = 2;
	const TYPE_REPLACE = 3;
	const TYPE_FLATTEN = 4;
	const TYPE_FLATTENALL = 5;
	const TYPE_DRAIN = 6;
	const TYPE_LEAFBLOWER = 7;
	const TYPE_CLEAN = 8;
	const TYPE_BIOME = 9;
	const TYPE_CLEANENTITIES = 10;
	const TYPE_MELT = 11;
	const TYPE_EXPAND = 12;
	const TYPE_RAISE = 13;
	
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
	
	/**
	 * @return Loader
	 */
	public function getMain(): Loader {
		return $this->main;
	}
	
	/**
	 * Returns the level the type is used in.
	 *
	 * @return Level
	 */
	public function getLevel(): Level {
		return $this->level;
	}
	
	/**
	 * Returns the player that used the type.
	 *
	 * @return Player
	 */
	public function getPlayer(): Player {
		return $this->player;
	}
	
	/**
	 * Returns the biome ID in case of a BiomeType
	 *
	 * @return int|null
	 */
	public function getBiome(): int {
		if($this instanceof BiomeType) {
			return $this->biome;
		}
		return null;
	}
	
	/**
	 * Returns the blocks the type is being executed upon.
	 *
	 * @return array
	 */
	public function getBlocks(): array {
		return $this->blocks;
	}
	
	/**
	 * Returns the center in case of a Flatten- or LayerType.
	 *
	 * @return Position|null
	 */
	public function getCenter(): Position {
		if($this instanceof FlattenType || $this instanceof FlattenallType || $this instanceof LayerType) {
			return $this->center;
		}
		return null;
	}
	
	/**
	 * Returns the obsolete blocks in case of a ReplaceType.
	 *
	 * @return array|null
	 */
	public function getObsolete(): array {
		if($this instanceof ReplaceType) {
			return $this->obsolete;
		}
		return null;
	}
}
