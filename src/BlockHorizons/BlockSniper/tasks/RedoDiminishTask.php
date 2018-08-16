<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockSniper\tasks;

use BlockHorizons\BlockSniper\revert\Revert;
use BlockHorizons\BlockSniper\sessions\SessionManager;

class RedoDiminishTask extends BaseTask {

	public function onRun(int $currentTick): void {
		foreach($this->loader->getServer()->getOnlinePlayers() as $player) {
			if(!SessionManager::playerSessionExists($player)) {
				continue;
			}
			if(($store = SessionManager::getPlayerSession($player)->getRevertStorer())->redoStorageExists()) {
				if($store->getLastRedoActivity() >= 180) {
					$store->unsetOldestRevert(Revert::TYPE_REDO);
				}
			}
		}
	}
}