<?php

namespace BlockHorizons\BlockSniper\tasks;

use BlockHorizons\BlockSniper\Loader;
use BlockHorizons\BlockSniper\undo\UndoStorer;
use pocketmine\scheduler\PluginTask;

abstract class BaseTask extends PluginTask {
	
	public $loader;
	
	public function __construct(Loader $loader) {
		parent::__construct($loader);
		$this->loader = $loader;
	}
	
	/**
	 * @return UndoStorer
	 */
	public function getUndoStore(): UndoStorer {
		return $this->getLoader()->getUndoStore();
	}
	
	/**
	 * @return Loader
	 */
	public function getLoader(): Loader {
		return $this->loader;
	}
}