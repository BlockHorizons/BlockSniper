<?php

namespace Sandertv\BlockSniper\commands;

use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat as TF;
use Sandertv\BlockSniper\Loader;

class BlockSniperCommand extends BaseCommand {
	
	public function __construct(Loader $owner) {
		parent::__construct($owner, "blocksniper", "Get information or change things related to BlockSniper", "[language|reload] [lang]", ["bs"]);
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
		
		if(!isset($args[0])) {
			$sender->sendMessage(TF::AQUA . "[BlockSniper] Information\n" .
				TF::GREEN . "Version: " . TF::YELLOW . Loader::VERSION . "\n" .
				TF::GREEN . "Target API: " . TF::YELLOW . Loader::API_TARGET . "\n" .
				TF::GREEN . "Author: " . TF::YELLOW . "Sandertv");
			return true;
		}
		
		switch(strtolower($args[0])) {
			/*case "language":
				$this->getSettings()->set("Message-Language", $args[1]);
				$this->getSettings()->save();
				$sender->sendMessage(TF::GREEN . $this->getPlugin()->getTranslation("commands.succeed.language"));
				return true;*/
			
			case "reload":
				$sender->sendMessage(TF::GREEN . "Reloading...");
				$this->getPlugin()->reloadAll();
				return true;
			
			default:
				$sender->sendMessage(TF::AQUA . "[BlockSniper] Information\n" .
					TF::GREEN . "Version: " . TF::YELLOW . Loader::VERSION . "\n" .
					TF::GREEN . "Target API: " . TF::YELLOW . Loader::API_TARGET . "\n" .
					TF::GREEN . "Author: " . TF::YELLOW . "Sandertv");
				return true;
		}
	}
}
