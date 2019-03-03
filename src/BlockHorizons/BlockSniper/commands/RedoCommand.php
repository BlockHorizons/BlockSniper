<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\commands;

use BlockHorizons\BlockSniper\data\Translation;
use BlockHorizons\BlockSniper\Loader;
use BlockHorizons\BlockSniper\revert\Revert;
use BlockHorizons\BlockSniper\sessions\SessionManager;
use pocketmine\Player;
use pocketmine\utils\TextFormat as TF;

class RedoCommand extends BaseCommand{

	public function __construct(Loader $loader){
		parent::__construct($loader, "redo", Translation::COMMANDS_REDO_DESCRIPTION);
		$redoCount =& $this->int("count", true)->default(1)->value();
		
		$this->onPlayerExec(function(Player $player) use($redoCount) {
			if(!SessionManager::getPlayerSession($player)->getRevertStore()->redoStorageExists()){
				$player->sendMessage($this->getWarning() . Translation::get(Translation::COMMANDS_REDO_NO_REDO));
				return;
			}

			$redoAmount = min($redoCount, SessionManager::getPlayerSession($player)->getRevertStore()->getTotalStores(Revert::TYPE_REDO));
			SessionManager::getPlayerSession($player)->getRevertStore()->restoreLatestRevert(Revert::TYPE_REDO, $redoAmount);
			$player->sendMessage(TF::GREEN . Translation::get(Translation::COMMANDS_REDO_SUCCESS) . TF::AQUA . " (" . $redoAmount . ")");
		});
	}
}
