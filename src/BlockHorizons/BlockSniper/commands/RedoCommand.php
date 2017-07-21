<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockSniper\commands;

use BlockHorizons\BlockSniper\Loader;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\utils\TextFormat as TF;

class RedoCommand extends BaseCommand {

	public function __construct(Loader $loader) {
		parent::__construct($loader, "redo", "Redo your last BlockSniper modification", "/redo [amount]", []);
	}

	public function execute(CommandSender $sender, string $commandLabel, array $args): bool {
		if(!$this->testPermission($sender)) {
			$this->sendNoPermission($sender);
			return true;
		}

		if(!$sender instanceof Player) {
			$this->sendConsoleError($sender);
			return true;
		}

		if(!$this->getLoader()->getUndoStorer()->redoStorageExists($sender)) {
			$sender->sendMessage(TF::RED . "[Warning] " . $this->getLoader()->getTranslation("commands.errors.no-modifications"));
			return true;
		}

		$redoAmount = 1;
		if(isset($args[0])) {
			$redoAmount = $args[0];
			if($redoAmount > ($totalRedo = $this->getLoader()->getUndoStorer()->getTotalRedoStores($sender))) {
				$redoAmount = $totalRedo;
			}
		}

		$this->getLoader()->getUndoStorer()->restoreLatestRedo($redoAmount, $sender);
		$sender->sendMessage(TF::GREEN . $this->getLoader()->getTranslation("commands.succeed.undo") . TF::AQUA . " (" . $redoAmount . ")");
		return true;
	}
}
