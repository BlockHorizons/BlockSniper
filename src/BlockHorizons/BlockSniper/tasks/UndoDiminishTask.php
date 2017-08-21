<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockSniper\tasks;

use BlockHorizons\BlockSniper\Loader;
use BlockHorizons\BlockSniper\sessions\SessionManager;
use BlockHorizons\BlockSniper\undo\Revert;

class UndoDiminishTask extends BaseTask {

	public function __construct(Loader $loader) {
		parent::__construct($loader);
	}

	public function onRun(int $currentTick) {
		foreach($this->getLoader()->getServer()->getOnlinePlayers() as $player) {
			if(!$player->hasPermission("blocksniper.command.brush")) {
				continue;
			}
			if(($storer = SessionManager::getPlayerSession($player)->getRevertStorer())->undoStorageExists()) {
				if($storer->getLastUndoActivity() >= 180) {
					$storer->unsetOldestRevert(Revert::TYPE_UNDO);
				}
			}
		}
	}
}