<?php

namespace BlockHorizons\BlockSniper\tasks;

use BlockHorizons\BlockSniper\Loader;

class RedoDiminishTask extends BaseTask {

	public function __construct(Loader $owner) {
		parent::__construct($owner);
	}

	public function onRun($currentTick) {
		foreach($this->getLoader()->getServer()->getOnlinePlayers() as $player) {
			if($this->getUndoStore()->redoStorageExists($player)) {
				if($this->getUndoStore()->getLastRedoActivity($player) >= 180) {
					$this->getUndoStore()->unsetOldestRedo($player);
				}
			}
		}
	}
}