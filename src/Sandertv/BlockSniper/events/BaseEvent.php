<?php

namespace Sandertv\BlockSniper\events;

use pocketmine\event\plugin\PluginEvent;
use Sandertv\BlockSniper\Loader;

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
