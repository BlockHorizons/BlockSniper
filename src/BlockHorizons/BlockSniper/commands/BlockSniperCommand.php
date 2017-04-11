<?php

namespace BlockHorizons\BlockSniper\commands;

use BlockHorizons\BlockSniper\Loader;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat as TF;

class BlockSniperCommand extends BaseCommand {
	
	public function __construct(Loader $loader) {
		parent::__construct($loader, "blocksniper", "Get information or change things related to BlockSniper", "[language|reload] [lang]", ["bs"]);
		$this->setPermission("blocksniper.command.blocksniper");
		$this->setUsage(TF::RED . "[Usage] /blocksniper [language [lang]");
	}
	
	public function execute(CommandSender $sender, $commandLabel, array $args) {
		if(!$this->testPermission($sender)) {
			$this->sendNoPermission($sender);
		}
		
		if(!isset($args[0])) {
			$sender->sendMessage(TF::AQUA . "[BlockSniper] Information\n" .
				TF::GREEN . "Version: " . TF::YELLOW . Loader::VERSION . "\n" .
				TF::GREEN . "Target API: " . TF::YELLOW . Loader::API_TARGET . "\n" .
				TF::GREEN . "Author: " . TF::YELLOW . "BlockHorizons");
			return true;
		}
		
		switch(strtolower($args[0])) {
			case "language":
				if(!in_array(strtolower($args[1]), $this->getLoader()->availableLanguages)) {
					$sender->sendMessage(TF::RED . "That language doesn't exist. Please try again.");
					return true;
				}
				$this->getSettings()->set("Message-Language", $args[1]);
				$sender->sendMessage(TF::GREEN . $this->getLoader()->getTranslation("commands.succeed.language"));
				return true;
			
			case "reload":
				$sender->sendMessage(TF::GREEN . "Reloading...");
				$this->getLoader()->reloadAll();
				return true;
			
			default:
				$sender->sendMessage(TF::AQUA . "[BlockSniper] Information\n" .
					TF::GREEN . "Version: " . TF::YELLOW . Loader::VERSION . "\n" .
					TF::GREEN . "Target API: " . TF::YELLOW . Loader::API_TARGET . "\n" .
					TF::GREEN . "Author: " . TF::YELLOW . "BlockHorizons");
				return true;
		}
	}
}
