<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockSniper\commands;

use BlockHorizons\BlockSniper\data\ConfigData;
use BlockHorizons\BlockSniper\data\Translation;
use BlockHorizons\BlockSniper\Loader;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\PluginIdentifiableCommand;
use pocketmine\Player;
use pocketmine\plugin\Plugin;
use pocketmine\utils\TextFormat as TF;

abstract class BaseCommand extends Command implements PluginIdentifiableCommand {

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
	public function sendConsoleError(CommandSender $sender): void {
		$sender->sendMessage($this->getWarning() . (new Translation(Translation::COMMANDS_COMMON_INVALID_SENDER))->getMessage());
	}

	/**
	 * @return Loader
	 */
	public function getLoader(): Loader {
		return $this->loader;
	}

	/**
	 * @return Plugin
	 */
	public function getPlugin(): Plugin {
		return $this->loader;
	}

	/**
	 * @param CommandSender $sender
	 */
	public function sendNoPermission(CommandSender $sender): void {
		$sender->sendMessage($this->getWarning() . (new Translation(Translation::COMMANDS_COMMON_NO_PERMISSION))->getMessage());
	}

	/**
	 * @return string
	 */
	public function getWarning(): string {
		return TF::RED . (new Translation(Translation::COMMANDS_COMMON_WARNING_PREFIX))->getMessage();
	}

	/**
	 * @return ConfigData
	 */
	public function getSettings(): ConfigData {
		return $this->getLoader()->getSettings();
	}
}
