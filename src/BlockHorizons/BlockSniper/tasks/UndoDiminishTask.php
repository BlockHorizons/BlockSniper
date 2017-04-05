<?php

namespace BlockHorizons\BlockSniper\tasks;

use BlockHorizons\BlockSniper\Loader;

class UndoDiminishTask extends BaseTask {
	
	public function __construct(Loader $owner) {
		parent::__construct($owner);
	}
	
	public function onRun($currentTick) {
		foreach($this->getLoader()->getServer()->getOnlinePlayers() as $player) {
			if($this->getUndoStore()->undoStorageExists($player)) {
				if($this->getUndoStore()->getLastUndoActivity($player) >= 180) {
					$this->getUndoStore()->unsetOldestUndo($player);
				}
			}
		}
	}
}