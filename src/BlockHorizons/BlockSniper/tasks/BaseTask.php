<?php

namespace BlockHorizons\BlockSniper\tasks;

use BlockHorizons\BlockSniper\Loader;
use BlockHorizons\BlockSniper\undo\UndoStorer;
use pocketmine\scheduler\PluginTask;

abstract class BaseTask extends PluginTask {
	
	protected $loader;
	
	public function __construct(Loader $loader) {
		parent::__construct($loader);
		$this->loader = $loader;
	}
	
	/**
	 * @return UndoStorer
	 */
	public function getUndoStorer(): UndoStorer {
		return $this->getLoader()->getUndoStorer();
	}
	
	/**
	 * @return Loader
	 */
	public function getLoader(): Loader {
		return $this->loader;
	}
}