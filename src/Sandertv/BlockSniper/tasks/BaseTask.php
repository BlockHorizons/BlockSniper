<?php

namespace Sandertv\BlockSniper\tasks;

use pocketmine\scheduler\PluginTask;
use Sandertv\BlockSniper\Loader;
use Sandertv\BlockSniper\undo\UndoStorer;

abstract class BaseTask extends PluginTask {
	
	public $loader;
	
	public function __construct(Loader $loader) {
		parent::__construct($loader);
		$this->owner = $loader;
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