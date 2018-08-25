<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\events;

use BlockHorizons\BlockSniper\brush\BaseShape;
use BlockHorizons\BlockSniper\brush\BaseType;
use pocketmine\event\Cancellable;
use pocketmine\level\Level;
use pocketmine\Player;

class BrushUseEvent extends BlockSniperEvent implements Cancellable{

	/** @var null */
	public static $handlerList = null;

	/** @var Player */
	public $player = null;
	/** @var BaseType */
	public $type = null;
	/** @var BaseShape */
	public $shape = null;

	public function __construct(Player $player, BaseShape $shape, BaseType $type){
		$this->player = $player;
		$this->type = $type;
		$this->shape = $shape;
	}

	/**
	 * @return Level
	 */
	public function getLevel() : Level{
		return $this->getPlayer()->getLevel();
	}

	/**
	 * Returns the player that used the Brush.
	 * @return Player
	 */
	public function getPlayer() : Player{
		return $this->player;
	}

	/**
	 * Returns the type of the player that used the Brush. (Object)
	 *
	 * @return BaseType
	 */
	public function getType() : BaseType{
		return $this->type;
	}

	/**
	 * Returns the shape of the player that used the Brush. (Object)
	 *
	 * @return BaseShape
	 */
	public function getShape() : BaseShape{
		return $this->shape;
	}

	/**
	 * Returns the *approximate* amount of blocks in the shape given.
	 *
	 * @return int
	 */
	public function getApproximateProcessedBlocks() : int{
		return $this->shape->getApproximateProcessedBlocks();
	}
}
