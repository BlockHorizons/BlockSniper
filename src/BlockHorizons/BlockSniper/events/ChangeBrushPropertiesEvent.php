<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\events;

use pocketmine\Player;

class ChangeBrushPropertiesEvent extends BlockSniperEvent{

	const ACTION_RESET_BRUSH = 0;
	const ACTION_CHANGE_SIZE = 1;
	const ACTION_CHANGE_HEIGHT = 2;
	const ACTION_CHANGE_TYPE = 3;
	const ACTION_CHANGE_SHAPE = 4;

	const ACTION_CHANGE_BLOCKS = 5;
	const ACTION_CHANGE_BIOME = 6;
	const ACTION_CHANGE_OBSOLETE = 7;
	const ACTION_CHANGE_DECREMENT = 8;
	const ACTION_CHANGE_HOLLOW = 9;
	const ACTION_CHANGE_TREE = 10;
	const ACTION_CHANGE_PERFECT = 11;

	/** @var null */
	public static $handlerList = null;

	/** @var Player */
	public $player = null;
	/** @var int */
	public $action = 0;
	/** @var mixed */
	public $value = null;

	public function __construct(Player $player, int $action, $value){
		$this->player = $player;
		$this->action = $action;
		$this->value = $value;
	}

	/**
	 * Returns the player that changed their Brush.
	 *
	 * @return Player
	 */
	public function getPlayer() : Player{
		return $this->player;
	}

	/**
	 * Returns the action of the modification.
	 *
	 * @return int
	 */
	public function getAction() : int{
		return $this->action;
	}

	/**
	 * Returns the input arguments from the Brush modification.
	 *
	 * @return mixed
	 */
	public function getActionValue(){
		return $this->value;
	}
}