<?php

namespace Sandertv\BlockSniper\events;

use pocketmine\event\plugin\PluginEvent;

abstract class BaseEvent extends PluginEvent {
	
	public function __construct($owner) {
		$this->owner = $owner;
	}
	
	public function getOwner() {
		return $this->owner;
	}
}
