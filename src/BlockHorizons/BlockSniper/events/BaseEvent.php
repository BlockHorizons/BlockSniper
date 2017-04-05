<?php

namespace BlockHorizons\BlockSniper\events;

use pocketmine\event\plugin\PluginEvent;
use BlockHorizons\BlockSniper\Loader;

abstract class BaseEvent extends PluginEvent {
	
	protected $loader;
	
	public function __construct(Loader $loader) {
		parent::__construct($loader);
		$this->loader = $loader;
	}
	
	public function getLoader() {
		return $this->loader;
	}
}
