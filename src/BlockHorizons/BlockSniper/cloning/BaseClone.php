<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\cloning;

use BlockHorizons\BlockSniper\brush\Shape;
use pocketmine\level\Level;
use pocketmine\level\Position;
use pocketmine\Player;
use function defined;
use function strtolower;
use function strtoupper;

abstract class BaseClone{

	public const TYPE_COPY = 0;
	public const TYPE_TEMPLATE = 1;
	public const TYPE_SCHEMATIC = 2;

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
	/** @var Shape */
	protected $shape = [];

	/**
	 * @param Player   $player
	 * @param bool     $saveAir
	 * @param Position $center
	 * @param Shape    $shape
	 * @param string   $name
	 */
	public function __construct(Player $player, bool $saveAir, Position $center, Shape $shape, string $name = ""){
		$this->player = $player;
		$this->level = $player->getLevel();
		$this->saveAir = $saveAir;
		$this->center = $center;
		$this->shape = $shape;
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
	 * @return Shape
	 */
	public function getShape() : Shape{
		return $this->shape;
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