<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockSniper\tasks;

use BlockHorizons\BlockSniper\Loader;

class UndoDiminishTask extends BaseTask {

	public function __construct(Loader $loader) {
		parent::__construct($loader);
	}

	public function onRun(int $currentTick) {
		foreach($this->getLoader()->getServer()->getOnlinePlayers() as $player) {
			if($this->getUndoStorer()->undoStorageExists($player)) {
				if($this->getUndoStorer()->getLastUndoActivity($player) >= 180) {
					$this->getUndoStorer()->unsetOldestUndo($player);
				}
			}
		}
	}
}