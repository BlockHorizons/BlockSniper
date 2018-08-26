<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\cloning;

use pocketmine\block\Block;
use pocketmine\level\Level;
use pocketmine\level\Position;
use pocketmine\Player;

abstract class BaseClone{

	const TYPE_COPY = 0;
	const TYPE_TEMPLATE = 1;
	const TYPE_SCHEMATIC = 2;

	/** @var Level */
	public $level = null;

	/** @var Player */
	protected $player = null;
	/** @var string */
	protected $name = "";
	/** @var Position */
	protected $center = null;
	/** @var bool */
	protected $saveAir = false;
	/** @var Block[] */
	protected $blocks = [];

	/**
	 * @param Player   $player
	 * @param bool     $saveAir
	 * @param Position $center
	 * @param Block[]  $blocks
	 * @param string   $name
	 */
	public function __construct(Player $player, bool $saveAir, Position $center, array $blocks, string $name = ""){
		$this->player = $player;
		$this->level = $player->getLevel();
		$this->saveAir = $saveAir;
		$this->center = $center;
		$this->blocks = $blocks;
		$this->name = $name;
	}

	/**
	 * @param string $type
	 *
	 * @return bool
	 */
	public static function isCloneType(string $type) : bool{
		$cloneTypeConst = strtoupper("type_" . $type);
		if(defined("self::$cloneTypeConst")){
			return true;
		}

		return false;
	}

	/**
	 * Returns the level the clone is made in.
	 *
	 * @return Level
	 */
	public function getLevel() : Level{
		return $this->level;
	}

	/**
	 * Returns the center block of the clone.
	 *
	 * @return Position
	 */
	public function getCenter() : Position{
		return $this->center;
	}

	/**
	 * Returns all blocks that are being cloned.
	 *
	 * @return Block[]
	 */
	public function getBlocks() : array{
		return $this->blocks;
	}

	/**
	 * Returns the permission required to use the clone type.
	 *
	 * @return string
	 */
	public function getPermission() : string{
		return "blocksniper.clone." . strtolower($this->getName());
	}

	public abstract function getName() : string;

	public abstract function saveClone() : void;
}