<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockSniper\tasks;

use BlockHorizons\BlockSniper\revert\Revert;
use BlockHorizons\BlockSniper\sessions\SessionManager;

class RedoDiminishTask extends BaseTask {

	public function onRun(int $currentTick): void {
		foreach($this->getLoader()->getServer()->getOnlinePlayers() as $player) {
			if(!SessionManager::playerSessionExists($player)) {
				continue;
			}
			if(($storer = SessionManager::getPlayerSession($player)->getRevertStorer())->redoStorageExists()) {
				if($storer->getLastRedoActivity() >= 180) {
					$storer->unsetOldestRevert(Revert::TYPE_REDO);
				}
			}
		}
	}
}