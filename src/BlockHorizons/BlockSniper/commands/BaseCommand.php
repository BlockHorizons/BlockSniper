<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\commands;

use BlockHorizons\BlockSniper\data\Translation;
use BlockHorizons\BlockSniper\Loader;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\PluginIdentifiableCommand;
use pocketmine\Player;
use pocketmine\plugin\Plugin;
use pocketmine\utils\TextFormat as TF;

abstract class BaseCommand extends Command implements PluginIdentifiableCommand{

	/** @var Loader */
	protected $loader = null;
	/** @var bool */
	protected $consoleUsable = false;

	public function __construct(Loader $loader, string $name, string $description, string $usageMessage, array $aliases = [], bool $consoleUsable = false){
		parent::__construct($name, Translation::get($description), "[Usage] " . $usageMessage, $aliases);
		$this->loader = $loader;
		$this->consoleUsable = $consoleUsable;
		$this->setPermission("blocksniper.command." . $name);
	}

	/**
	 * @param CommandSender $sender
	 */
	public function sendConsoleError(CommandSender $sender) : void{
		$sender->sendMessage($this->getWarning() . Translation::get(Translation::COMMANDS_COMMON_INVALID_SENDER));
	}

	/**
	 * @return string
	 */
	public function getWarning() : string{
		return TF::RED . Translation::get(Translation::COMMANDS_COMMON_WARNING_PREFIX);
	}

	/**
	 * @return Plugin
	 */
	public function getPlugin() : Plugin{
		return $this->loader;
	}

	/**
	 * @param CommandSender $sender
	 */
	public function sendNoPermission(CommandSender $sender) : void{
		$sender->sendMessage($this->getWarning() . Translation::get(Translation::COMMANDS_COMMON_NO_PERMISSION));
	}

	/**
	 * @param CommandSender $sender
	 * @param string        $commandLabel
	 * @param array         $args
	 */
	public function execute(CommandSender $sender, string $commandLabel, array $args) : void{
		if(!$this->testPermission($sender)){
			$this->sendNoPermission($sender);

			return;
		}
		if(!$this->consoleUsable && (!$sender instanceof Player)){
			$this->sendConsoleError($sender);

			return;
		}
		$this->onExecute($sender, $commandLabel, $args);
	}

	/**
	 * @param CommandSender $sender
	 * @param string        $commandLabel
	 * @param array         $args
	 */
	public abstract function onExecute(CommandSender $sender, string $commandLabel, array $args) : void;
}
