<?php

namespace BlockHorizons\BlockSniper\brush;

use BlockHorizons\BlockSniper\brush\types\BiomeType;
use BlockHorizons\BlockSniper\brush\types\FlattenallType;
use BlockHorizons\BlockSniper\brush\types\FlattenType;
use BlockHorizons\BlockSniper\brush\types\LayerType;
use BlockHorizons\BlockSniper\brush\types\ReplaceType;
use BlockHorizons\BlockSniper\brush\types\TreeType;
use BlockHorizons\BlockSniper\undo\UndoStorer;
use pocketmine\block\Block;
use pocketmine\level\Level;
use pocketmine\level\Position;
use pocketmine\Player;

abstract class BaseType {
	
	const MAX_WORLD_HEIGHT = 256;
	const MIN_WORLD_HEIGHT = 0;
	
	const TYPE_FILL = 0;
	const TYPE_OVERLAY = 1;
	const TYPE_LAYER = 2;
	const TYPE_REPLACE = 3;
	const TYPE_REPLACEALL = 4;
	const TYPE_FLATTEN = 5;
	const TYPE_FLATTENALL = 6;
	const TYPE_DRAIN = 7;
	const TYPE_LEAFBLOWER = 8;
	const TYPE_CLEAN = 9;
	const TYPE_BIOME = 10;
	const TYPE_CLEANENTITIES = 11;
	const TYPE_MELT = 12;
	const TYPE_EXPAND = 13;
	const TYPE_RAISE = 14;
	const TYPE_TOPLAYER = 15;
	const TYPE_SNOWCONE = 16;
	const TYPE_TREE = 17;

	public $player;

	protected $undoStorer;
	protected $level;
	protected $biome;
	protected $blocks;
	protected $center;
	protected $obsolete;
	protected $tree;

	/**
	 * @param UndoStorer $undoStorer
	 * @param Player     $player
	 * @param Level      $level
	 * @param Block[]    $blocks
	 */
	public function __construct(UndoStorer $undoStorer, Player $player, Level $level, array $blocks) {
		$this->undoStorer = $undoStorer;
		$this->player = $player;
		$this->level = $level;
		$this->blocks = $blocks;
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
		define(('BlockHorizons\BlockSniper\brush\BaseType\\' . $typeConst), $number);
		return true;
	}
	
	public abstract function getName(): string;
	
	public abstract function fillShape(): bool;
	
	/**
	 * @return UndoStorer
	 */
	public function getUndoStore(): UndoStorer {
		return $this->undoStorer;
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
	 * Returns the center in case of a Flatten-, Tree- or LayerType.
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
	
	/**
	 * Returns the tree ID of the tree type in case of a TreeType.
	 *
	 * @return int
	 */
	public function getTree(): int {
		if($this instanceof TreeType) {
			return $this->tree;
		}
		return null;
	}

	/**
	 * Returns the permission required to use the type.
	 *
	 * @return string
	 */
	public function getPermission(): string {
		return "blocksniper.type." . str_replace("hollow", "", str_replace(" ", "_", strtolower($this->getName())));
	}
}
