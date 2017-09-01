<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockSniper\events;

use BlockHorizons\BlockSniper\Loader;
use pocketmine\event\plugin\PluginEvent;

abstract class BaseEvent extends PluginEvent {

	/** @var Loader */
	protected $loader = null;

	public function __construct(Loader $loader) {
		parent::__construct($loader);
		$this->loader = $loader;
	}

	public function getLoader(): Loader {
		return $this->loader;
	}
}
