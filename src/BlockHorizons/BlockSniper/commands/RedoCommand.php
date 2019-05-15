<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\commands;

use BlockHorizons\BlockSniper\data\Translation;
use BlockHorizons\BlockSniper\Loader;
use BlockHorizons\BlockSniper\sessions\SessionManager;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\utils\TextFormat as TF;

class RedoCommand extends BaseCommand{

	public function __construct(Loader $loader){
		parent::__construct($loader, "redo", Translation::COMMANDS_REDO_DESCRIPTION, "/redo [amount]");
	}

	public function onExecute(CommandSender $sender, string $commandLabel, array $args) : void{
		/** @var Player $sender */
		$store = SessionManager::getPlayerSession($sender)->getRevertStore();
		if($store->getRedoCount() === 0){
			$sender->sendMessage($this->getWarning() . Translation::get(Translation::COMMANDS_REDO_NO_REDO));

			return;
		}

		$redoAmount = 1;
		if(isset($args[0])){
			$redoAmount = (int) $args[0];
			$totalRedo = $store->getRedoCount();
			if($redoAmount > $totalRedo || $args[0] === "all"){
				$redoAmount = $totalRedo;
			}
		}
		SessionManager::getPlayerSession($sender)->getRevertStore()->restoreLatestRedo($redoAmount);
		$sender->sendMessage(TF::GREEN . Translation::get(Translation::COMMANDS_REDO_SUCCESS) . TF::AQUA . " (" . $redoAmount . ")");
	}
}
