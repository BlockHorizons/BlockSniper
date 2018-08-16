<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockSniper\commands;

use BlockHorizons\BlockSniper\data\Translation;
use BlockHorizons\BlockSniper\Loader;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\PluginIdentifiableCommand;
use pocketmine\plugin\Plugin;
use pocketmine\utils\TextFormat as TF;

abstract class BaseCommand extends Command implements PluginIdentifiableCommand {

	/** @var Loader */
	protected $loader = null;

	public function __construct(Loader $loader, string $name, string $description = "", string $usageMessage, array $aliases = []) {
		parent::__construct($name, Translation::get($description), $usageMessage, $aliases);
		$this->loader = $loader;
		$this->setPermission("blocksniper.command." . $name);
		$this->setUsage(TF::RED . "[Usage] " . $usageMessage);
	}

	/**
	 * @param CommandSender $sender
	 */
	public function sendConsoleError(CommandSender $sender): void {
		$sender->sendMessage($this->getWarning() . Translation::get(Translation::COMMANDS_COMMON_INVALID_SENDER));
	}

	/**
	 * @return string
	 */
	public function getWarning(): string {
		return TF::RED . Translation::get(Translation::COMMANDS_COMMON_WARNING_PREFIX);
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
		$sender->sendMessage($this->getWarning() . Translation::get(Translation::COMMANDS_COMMON_NO_PERMISSION));
	}
}
