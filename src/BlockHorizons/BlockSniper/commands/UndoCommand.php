<?php

namespace BlockHorizons\BlockSniper\commands;

use BlockHorizons\BlockSniper\Loader;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\utils\TextFormat as TF;

class UndoCommand extends BaseCommand {
	
	public function __construct(Loader $loader) {
		parent::__construct($loader, "undo", "Undo your last BlockSniper modification", "", ["u"]);
		$this->setPermission("blocksniper.command.undo");
		$this->setUsage(TF::RED . "[Usage] /undo [amount]");
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
		
		if(!$this->getLoader()->getUndoStore()->undoStorageExists($sender)) {
			$sender->sendMessage(TF::RED . "[Warning] " . $this->getLoader()->getTranslation("commands.errors.no-modifications"));
			return true;
		}
		
		$undoAmount = 1;
		if(isset($args[0])) {
			if(is_numeric($args[0])) {
				$undoAmount = $args[0];
				if($undoAmount > ($totalUndo = $this->getLoader()->getUndoStore()->getTotalUndoStores($sender))) {
					$undoAmount = $totalUndo;
				}
			}
		}
		
		$this->getLoader()->getUndoStore()->restoreLatestUndo($undoAmount, $sender);
		$sender->sendMessage(TF::GREEN . $this->getLoader()->getTranslation("commands.succeed.undo") . TF::AQUA . " (" . $undoAmount . ")");
		return true;
	}
}
