<?php

namespace Sandertv\BlockSniper\tasks;

use pocketmine\scheduler\PluginTask;
use Sandertv\BlockSniper\Loader;
use Sandertv\BlockSniper\UndoStorer;

abstract class BaseTask extends PluginTask {
	
	public $owner;
	
	public function __construct(Loader $owner) {
		parent::__construct($owner);
		$this->owner = $owner;
	}
	
	/**
	 * @return Loader
	 */
	public function getPlugin(): Loader {
		return $this->owner;
	}
	
	/**
	 * @return UndoStorer
	 */
	public function getUndoStore(): UndoStorer {
		return $this->getPlugin()->getUndoStore();
	}
}