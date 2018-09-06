<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\tasks;

use BlockHorizons\BlockSniper\revert\Revert;
use BlockHorizons\BlockSniper\sessions\SessionManager;

class UndoDiminishTask extends BaseTask{

	public function onRun(int $currentTick) : void{
		foreach($this->loader->getServer()->getOnlinePlayers() as $player){
			if(!SessionManager::playerSessionExists($player->getName())){
				continue;
			}
			if(($store = SessionManager::getPlayerSession($player)->getRevertStore())->undoStorageExists()){
				if($store->getLastUndoActivity() >= 180){
					$store->unsetOldestRevert(Revert::TYPE_UNDO);
				}
			}
		}
	}
}