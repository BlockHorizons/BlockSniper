<?php

namespace BlockHorizons\BlockSniper\commands;

use BlockHorizons\BlockSniper\Loader;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\utils\TextFormat as TF;

class CancelCommand extends BaseCommand {

	public function __construct(Loader $loader) {
		parent::__construct($loader, "cancel", "Cancels a working tick spread operation of BlockSniper", "/cancel", ["canceloperation", "stopoperation", "cancelbrush", "c"]);
	}

	public function execute(CommandSender $sender, $commandLabel, array $args): bool {
		if(!$this->testPermission($sender)) {
			$this->sendNoPermission($sender);
			return true;
		}

		if(!$sender instanceof Player) {
			$this->sendConsoleError($sender);
			return true;
		}

		if(!$this->getLoader()->getWorkerManager()->hasWorkingWorker($sender)) {
			$sender->sendMessage(TF::RED . "[Error] " . $this->getLoader()->getTranslation("commands.errors.no-cancellable"));
			return true;
		}

		$this->getLoader()->getWorkerManager()->getFirstWorkingWorker($sender)->clearOccupation();
		$sender->sendMessage(TF::GREEN . $this->getLoader()->getTranslation("commands.succeed.cancel"));
		return true;
	}
}