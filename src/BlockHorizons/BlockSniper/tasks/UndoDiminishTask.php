<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockSniper\tasks;

use BlockHorizons\BlockSniper\Loader;
use BlockHorizons\BlockSniper\undo\Revert;

class UndoDiminishTask extends BaseTask {

	public function __construct(Loader $loader) {
		parent::__construct($loader);
	}

	public function onRun(int $currentTick) {
		foreach($this->getLoader()->getServer()->getOnlinePlayers() as $player) {
			if($this->getRevertStorer()->undoStorageExists($player)) {
				if($this->getRevertStorer()->getLastUndoActivity($player) >= 180) {
					$this->getRevertStorer()->unsetOldestRevert($player, Revert::TYPE_UNDO);
				}
			}
		}
	}
}