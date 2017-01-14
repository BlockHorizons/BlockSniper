<?php

namespace Sandertv\BlockSniper\commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\PluginIdentifiableCommand;
use pocketmine\utils\TextFormat as TF;
use Sandertv\BlockSniper\Loader;

abstract class BaseCommand extends Command implements PluginIdentifiableCommand {
	
	public function __construct(Loader $owner, $name, $description = "", $usageMessage = null, array $aliases = []) {
		parent::__construct($name, $description, $usageMessage, $aliases);
		$this->owner = $owner;
		$this->usageMessage = "";
	}
	
	/**
	 * @return Loader
	 */
	public function getPlugin(): Loader {
		return $this->owner;
	}
	
	/**
	 * @param CommandSender $sender
	 */
	public function sendConsoleError(CommandSender $sender) {
		$sender->sendMessage(TF::RED . "[Warning] " . $this->getPlugin()->getTranslation("commands.errors.console-use"));
	}
	
	public function sendNoPermission(CommandSender $sender) {
		$sender->sendMessage(TF::RED . "[Warning] " . $this->getPlugin()->getTranslation("commands.errors.no-permission"));
	}
	
	public function getSettings() {
		return $this->getPlugin()->settings;
	}
}
