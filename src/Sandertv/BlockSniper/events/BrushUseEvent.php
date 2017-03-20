<?php

namespace Sandertv\BlockSniper\events;

use pocketmine\event\Cancellable;
use pocketmine\level\Level;
use pocketmine\Player;
use Sandertv\BlockSniper\brush\BaseShape;
use Sandertv\BlockSniper\brush\BaseType;
use Sandertv\BlockSniper\Loader;

class BrushUseEvent extends BaseEvent implements Cancellable {
	
	public static $handlerList = null;
	
	public $player;
	public $type;
	public $shape;
	
	public function __construct(Loader $owner, Player $player, BaseShape $shape, BaseType $type) {
		parent::__construct($owner);
		$this->player = $player;
		$this->type = $type;
		$this->shape = $shape;
	}
	
	/**
	 * @return Level
	 */
	public function getLevel(): Level {
		return $this->getPlayer()->getLevel();
	}
	
	/**
	 * Returns the player that used the Brush.
	 * @return Player
	 */
	public function getPlayer(): Player {
		return $this->player;
	}
	
	/**
	 * Returns the type of the player that used the Brush. (Object)
	 *
	 * @return BaseType
	 */
	public function getType(): BaseType {
		return $this->type;
	}
	
	/**
	 * Returns the shape of the player that used the Brush. (Object)
	 *
	 * @return BaseShape
	 */
	public function getShape(): BaseShape {
		return $this->shape;
	}
	
	/**
	 * Returns the *approximate* amount of blocks in the shape given.
	 *
	 * @return int
	 */
	public function getApproximateProcessedBlocks(): int {
		return $this->shape->getApproximateProcessedBlocks();
	}
}
