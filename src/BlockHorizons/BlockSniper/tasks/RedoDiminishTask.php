<?php

namespace BlockHorizons\BlockSniper\tasks;

use BlockHorizons\BlockSniper\Loader;

class RedoDiminishTask extends BaseTask {

	public function __construct(Loader $loader) {
		parent::__construct($loader);
	}

	public function onRun($currentTick) {
		foreach($this->getLoader()->getServer()->getOnlinePlayers() as $player) {
			if($this->getUndoStorer()->redoStorageExists($player)) {
				if($this->getUndoStorer()->getLastRedoActivity($player) >= 180) {
					$this->getUndoStorer()->unsetOldestRedo($player);
				}
			}
		}
	}
}