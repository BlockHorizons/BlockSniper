<?php

namespace Sandertv\BlockSniper\events;

use pocketmine\event\plugin\PluginEvent;
use pocketmine\Player;
use Sandertv\BlockSniper\Loader;

class ChangeBrushPropertiesEvent extends PluginEvent {
	
	const ACTION_CHANGE_SIZE = 0;
	const ACTION_CHANGE_HEIGHT = 1;
	const ACTION_CHANGE_TYPE = 2;
	const ACTION_CHANGE_SHAPE = 3;
	
	const ACTION_CHANGE_BLOCKS = 4;
	const ACTION_CHANGE_BIOME = 5;
	const ACTION_CHANGE_OBSOLETE = 6;
	const ACTION_CHANGE_DECREMENT = 7;
	
	public static $handlerList = null;
	
	public $owner;
	public $player;
	public $action;
	public $value;
	
	public function __construct(Loader $owner, Player $player, int $action, $value) {
		parent::__construct($owner);
		$this->owner = $owner;
		$this->player = $player;
		$this->action = $action;
		$this->value = $value;
	}
	
	/**
	 * Returns the player that changed their Brush.
	 *
	 * @return Player
	 */
	public function getPlayer(): Player {
		return $this->player;
	}
	
	/**
	 * Returns the action of the modification.
	 *
	 * @return int
	 */
	public function getAction(): int {
		return $this->action;
	}
	
	/**
	 * Returns the input arguments from the Brush modification.
	 *
	 * @return mixed
	 */
	public function getActionValue() {
		return $this->value;
	}
}