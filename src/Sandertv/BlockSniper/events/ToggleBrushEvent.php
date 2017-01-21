<?php

namespace Sandertv\BlockSniper\events;

use pocketmine\event\Cancellable;
use pocketmine\Player;

class ToggleBrushEvent extends BaseEvent implements Cancellable {
	
	public static $handlerList = null;
	
	public $owner;
	public $player;
	public $originStatus;
	
	public function __construct($owner, Player $player, string $originStatus) {
		parent::__construct($owner);
		$this->owner = $owner;
		$this->player = $player;
		$this->originStatus = $originStatus;
	}
	
	/**
	 * Returns the status from the brush wand, before it was toggled.
	 *
	 * @return string
	 */
	public function getOriginalStatus(): string {
		return $this->originStatus;
	}
	
	/**
	 * Returns the status from the brush wand, after it was toggled.
	 *
	 * @return string
	 */
	public function getTargetStatus(): string {
		return ($this->originStatus === "enabled" ? "disabled" : "enabled");
	}
	
	/**
	 * Returns the player, from who the brush wand has been toggled.
	 *
	 * @return Player
	 */
	public function getPlayer(): Player {
		return $this->player;
	}
}