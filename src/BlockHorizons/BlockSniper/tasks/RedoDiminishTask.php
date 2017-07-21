<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockSniper\tasks;

use BlockHorizons\BlockSniper\Loader;
use BlockHorizons\BlockSniper\undo\Revert;

class RedoDiminishTask extends BaseTask {

	public function __construct(Loader $loader) {
		parent::__construct($loader);
	}

	public function onRun(int $currentTick) {
		foreach($this->getLoader()->getServer()->getOnlinePlayers() as $player) {
			if($this->getRevertStorer()->redoStorageExists($player)) {
				if($this->getRevertStorer()->getLastRedoActivity($player) >= 180) {
					$this->getRevertStorer()->unsetOldestRevert($player, Revert::TYPE_REDO);
				}
			}
		}
	}
}