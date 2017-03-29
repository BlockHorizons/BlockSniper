<?php

namespace Sandertv\BlockSniper\commands;

use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\utils\TextFormat as TF;
use Sandertv\BlockSniper\Loader;

class UndoCommand extends BaseCommand {
	
	public function __construct(Loader $owner) {
		parent::__construct($owner, "undo", "Undo your last BlockSniper modification", "", ["u"]);
		$this->setPermission("blocksniper.command.undo");
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
		
		if(!$this->getPlugin()->getUndoStore()->undoStorageExists()) {
			$sender->sendMessage(TF::RED . "[Warning] " . $this->getPlugin()->getTranslation("commands.errors.no-modifications"));
			return true;
		}
		
		$undoAmount = 1;
		if(isset($args[0])) {
			if(is_numeric($args[0])) {
				$undoAmount = $args[0];
				if($undoAmount > ($totalUndo = $this->getPlugin()->getUndoStore()->getTotalUndoStores())) {
					$undoAmount = $totalUndo;
				}
			}
		}
		
		$this->getPlugin()->getUndoStore()->restoreLastUndo($undoAmount);
		$sender->sendMessage(TF::GREEN . $this->getPlugin()->getTranslation("commands.succeed.undo") . TF::AQUA . " (" . $undoAmount . ")");
		return true;
	}
}
