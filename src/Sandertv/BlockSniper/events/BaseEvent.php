<?php

namespace Sandertv\BlockSniper\events;

use pocketmine\event\plugin\PluginEvent;
use Sandertv\BlockSniper\Loader;

abstract class BaseEvent extends PluginEvent {
	
	public function __construct(Loader $owner) {
		$this->owner = $owner;
	}
	
	public function getOwner() {
		return $this->owner;
	}
}
