<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\event;

use BlockHorizons\BlockSniper\brush\Shape;
use BlockHorizons\BlockSniper\brush\Type;
use pocketmine\event\Cancellable;
use pocketmine\event\CancellableTrait;
use pocketmine\player\Player;
use pocketmine\world\World;

class BrushUseEvent extends BlockSniperEvent implements Cancellable{
	use CancellableTrait;

	/** @var null */
	public static $handlerList = null;

	/** @var Player */
	public $player = null;
	/** @var Type */
	public $type = null;
	/** @var Shape */
	public $shape = null;

	public function __construct(Player $player, Shape $shape, Type $type){
		$this->player = $player;
		$this->type = $type;
		$this->shape = $shape;
	}

	/**
	 * @return World
	 */
	public function getWorld() : World{
		return $this->getPlayer()->getWorld();
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
	 * @return Type
	 */
	public function getType() : Type{
		return $this->type;
	}

	/**
	 * Returns the shape of the player that used the Brush. (Object)
	 *
	 * @return Shape
	 */
	public function getShape() : Shape{
		return $this->shape;
	}

	/**
	 * Returns the *approximate* amount of blocks in the shape given.
	 *
	 * @return int
	 */
	public function getApproximateProcessedBlocks() : int{
		return $this->shape->getBlockCount();
	}
}
