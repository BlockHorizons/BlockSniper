<?php

namespace Sandertv\BlockSniper\commands;

use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\utils\TextFormat as TF;
use Sandertv\BlockSniper\Loader;

class UndoCommand extends BaseCommand {
	
	public function __construct(Loader $owner) {
		parent::__construct($owner, "undo", "Undo your last BlockSniper modification", "", []);
		$this->setPermission("blocksniper.command.undo");
	}
	
	/**
	 * @param CommandSender $sender
	 * @param type          $commandLabel
	 * @param array         $args
	 *
	 * @return boolean
	 */
	public function execute(CommandSender $sender, $commandLabel, array $args) {
		if(!$this->testPermission($sender)) {
			$this->sendNoPermission($sender);
		}
		
		if(!$sender instanceof Player) {
			$this->sendConsoleError($sender);
			return true;
		}
		
		if(!$this->getPlugin()->undoStore->undoStorageExists()) {
			$sender->sendMessage(TF::RED . "[Warning] There are no modifications to undo.");
			return true;
		}
		
		$this->getPlugin()->undoStore->restoreLastUndo();
		$sender->sendMessage(TF::GREEN . "Succesfully undid the last modification.");
		return true;
	}
}
