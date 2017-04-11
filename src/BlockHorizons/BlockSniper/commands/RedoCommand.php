<?php

namespace BlockHorizons\BlockSniper\commands;

use BlockHorizons\BlockSniper\Loader;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\utils\TextFormat as TF;

class RedoCommand extends BaseCommand {

	public function __construct(Loader $loader) {
		parent::__construct($loader, "redo", "Redo your last BlockSniper modification", "", []);
		$this->setPermission("blocksniper.command.redo");
		$this->setUsage(TF::RED . "[Usage] /redo [amount]");
	}

	public function execute(CommandSender $sender, $commandLabel, array $args) {
		if(!$this->testPermission($sender)) {
			$this->sendNoPermission($sender);
			return true;
		}

		if(!$sender instanceof Player) {
			$this->sendConsoleError($sender);
			return true;
		}

		if(!$this->getLoader()->getUndoStore()->redoStorageExists($sender)) {
			$sender->sendMessage(TF::RED . "[Warning] " . $this->getLoader()->getTranslation("commands.errors.no-modifications"));
			return true;
		}

		$redoAmount = 1;
		if(isset($args[0])) {
			if(is_numeric($args[0])) {
				$redoAmount = $args[0];
				if($redoAmount > ($totalRedo = $this->getLoader()->getUndoStore()->getTotalRedoStores($sender))) {
					$redoAmount = $totalRedo;
				}
			}
		}

		$this->getLoader()->getUndoStore()->restoreLatestRedo($redoAmount, $sender);
		$sender->sendMessage(TF::GREEN . $this->getLoader()->getTranslation("commands.succeed.undo") . TF::AQUA . " (" . $redoAmount . ")");
		return true;
	}
}
