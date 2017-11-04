<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockSniper\commands;

use BlockHorizons\BlockSniper\data\Translation;
use BlockHorizons\BlockSniper\Loader;
use BlockHorizons\BlockSniper\revert\Revert;
use BlockHorizons\BlockSniper\sessions\SessionManager;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\utils\TextFormat as TF;

class RedoCommand extends BaseCommand {

	public function __construct(Loader $loader) {
		parent::__construct($loader, "redo", Translation::get(Translation::COMMANDS_REDO_DESCRIPTION), "/redo [amount]");
	}

	public function execute(CommandSender $sender, string $commandLabel, array $args): bool {
		if(!$this->testPermission($sender)) {
			$this->sendNoPermission($sender);
			return false;
		}

		if(!$sender instanceof Player) {
			$this->sendConsoleError($sender);
			return false;
		}

		if(!SessionManager::getPlayerSession($sender)->getRevertStorer()->redoStorageExists()) {
			$sender->sendMessage($this->getWarning() . Translation::get(Translation::COMMANDS_REDO_NO_REDO));
			return false;
		}

		$redoAmount = 1;
		if(isset($args[0])) {
			$redoAmount = (int) $args[0];
			if($redoAmount > ($totalRedo = SessionManager::getPlayerSession($sender)->getRevertStorer()->getTotalStores(Revert::TYPE_REDO))) {
				$redoAmount = $totalRedo;
			}
		}
		SessionManager::getPlayerSession($sender)->getRevertStorer()->restoreLatestRevert(Revert::TYPE_REDO, $redoAmount);
		$sender->sendMessage(TF::GREEN . Translation::get(Translation::COMMANDS_REDO_SUCCESS) . TF::AQUA . " (" . $redoAmount . ")");
		return true;
	}
}
