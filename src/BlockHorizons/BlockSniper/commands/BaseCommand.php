<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockSniper\commands;

use BlockHorizons\BlockSniper\data\ConfigData;
use BlockHorizons\BlockSniper\Loader;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\PluginIdentifiableCommand;
use pocketmine\Player;
use pocketmine\plugin\Plugin;
use pocketmine\utils\TextFormat as TF;

abstract class BaseCommand extends Command implements PluginIdentifiableCommand, OverloadedCommand {

	/** @var Loader */
	protected $loader = null;

	public function __construct(Loader $loader, $name, $description = "", $usageMessage = null, array $aliases = []) {
		parent::__construct($name, $description, $usageMessage, $aliases);
		$this->loader = $loader;
		$this->setPermission("blocksniper.command." . $name);
		$this->setUsage(TF::RED . "[Usage] " . $usageMessage);
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
	 * @return Plugin
	 */
	public function getPlugin(): Plugin {
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

	public function generateCustomCommandData(Player $player): array {
		$commandData = parent::generateCustomCommandData($player);
		$commandData["permission"] = $this->getPermission();
		$commandData["aliases"] = $this->getAliases();
		$commandData["overloads"]["default"]["input"]["parameters"] = CommandOverloads::getOverloads($this->getName());

		return $commandData;
	}
}
