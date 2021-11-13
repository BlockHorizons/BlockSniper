<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\event;

use pocketmine\player\Player;

class ChangeBrushPropertiesEvent extends BlockSniperEvent{

	public const ACTION_RESET_BRUSH = 0;
	public const ACTION_CHANGE_SIZE = 1;
	public const ACTION_CHANGE_HEIGHT = 2;
	public const ACTION_CHANGE_TYPE = 3;
	public const ACTION_CHANGE_SHAPE = 4;

	public const ACTION_CHANGE_BLOCKS = 5;
	public const ACTION_CHANGE_BIOME = 6;
	public const ACTION_CHANGE_OBSOLETE = 7;
	public const ACTION_CHANGE_DECREMENT = 8;
	public const ACTION_CHANGE_HOLLOW = 9;
	public const ACTION_CHANGE_TREE = 10;
	public const ACTION_CHANGE_PERFECT = 11;

	/** @var null */
	public static $handlerList = null;

	/** @var Player */
	public $player = null;
	/** @var int */
	public $action = 0;
	/** @var mixed */
	public $value = null;

	public function __construct(Player $player, int $action, mixed $value){
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