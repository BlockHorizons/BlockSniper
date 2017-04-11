<?php

namespace BlockHorizons\BlockSniper\commands;

use BlockHorizons\BlockSniper\data\ConfigData;
use BlockHorizons\BlockSniper\Loader;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\PluginIdentifiableCommand;
use pocketmine\utils\TextFormat as TF;

abstract class BaseCommand extends Command implements PluginIdentifiableCommand {
	
	protected $loader;
	
	public function __construct(Loader $loader, $name, $description = "", $usageMessage = null, array $aliases = []) {
		parent::__construct($name, $description, $usageMessage, $aliases);
		$this->loader = $loader;
		$this->usageMessage = "";
	}
	
	/**
	 * @param CommandSender $sender
	 */
	public function sendConsoleError(CommandSender $sender) {
		$sender->sendMessage(TF::RED . "[Warning] " . $this->getLoader()->getTranslation("commands.errors.console-use"));
	}
	
	/**
	 * @return Loader
	 */
	public function getLoader(): Loader {
		return $this->loader;
	}

	/**
	 * Annoying we have to implement this function. Messes up code consistency.
	 *
	 * @return Loader
	 */
	public function getPlugin(): Loader {
		return $this->loader;
	}
	
	/**
	 * @param CommandSender $sender
	 */
	public function sendNoPermission(CommandSender $sender) {
		$sender->sendMessage(TF::RED . "[Warning] " . $this->getLoader()->getTranslation("commands.errors.no-permission"));
	}
	
	/**
	 * @return ConfigData
	 */
	public function getSettings(): ConfigData {
		return $this->getLoader()->getSettings();
	}
}
