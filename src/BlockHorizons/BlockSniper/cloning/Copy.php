<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\cloning;

use BlockHorizons\BlockSniper\brush\Shape;
use BlockHorizons\BlockSniper\sessions\SessionManager;
use pocketmine\level\Level;
use pocketmine\level\Position;
use pocketmine\Player;

class Copy{

	public const TYPE_COPY = 0;
	public const TYPE_TEMPLATE = 1;
	public const TYPE_SCHEMATIC = 2;

	/** @var Level */
	public $level = null;

	/** @var Player */
	protected $player = null;
	/** @var Position */
	protected $centre = null;
	/** @var Shape */
	protected $shape = [];

	/**
	 * @param Player   $player
	 * @param Shape    $shape
	 */
	public function __construct(Player $player, Shape $shape){
		$offset = ($shape->maxY - $shape->minY) / 2;

		$this->player = $player;
		$this->level = $player->getLevel();
		$this->centre = Position::fromObject($shape->getCentre()->subtract(0, $offset), $player->getLevel());
		$this->shape = $shape;
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
	public function getCentre() : Position{
		return $this->centre;
	}

	/**
	 * @return Shape
	 */
	public function getShape() : Shape{
		return $this->shape;
	}

	public function save() : void {
		SessionManager::getPlayerSession($this->player)->getCloneStore()->saveCopy($this->shape, $this->centre);
	}
}