<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\command;

use BlockHorizons\BlockSniper\data\Translation;
use BlockHorizons\BlockSniper\Loader;
use BlockHorizons\BlockSniper\ui\window\ConfigurationMenuWindow;
use BlockHorizons\BlockSniper\ui\window\MainMenuWindow;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat as TF;
use function strtolower;

class BlockSniperCommand extends BaseCommand{

	private string $info;

	public function __construct(Loader $loader){
		parent::__construct($loader, "blocksniper", Translation::COMMANDS_BLOCKSNIPER_DESCRIPTION, "/blocksniper [menu|reload]", ["bs"], true);
		$this->info = TF::AQUA . "[BlockSniper] " . Translation::get(Translation::COMMANDS_BLOCKSNIPER_INFO) . "\n" .
			TF::GREEN . Translation::get(Translation::COMMANDS_BLOCKSNIPER_VERSION) . TF::YELLOW . Loader::VERSION . "\n" .
			TF::GREEN . Translation::get(Translation::COMMANDS_BLOCKSNIPER_TARGET_API) . TF::YELLOW . Loader::API_TARGET . "\n" .
			TF::GREEN . Translation::get(Translation::COMMANDS_BLOCKSNIPER_ORGANISATION) . TF::YELLOW . "BlockHorizons (https://github.com/BlockHorizons/BlockSniper)\n" .
			TF::GREEN . Translation::get(Translation::COMMANDS_BLOCKSNIPER_AUTHORS) . TF::YELLOW . "Sandertv (@Sandertv), Chris-Prime (@PrimusLV)";
	}

	public function onExecute(CommandSender $sender, string $commandLabel, array $args) : void{
		if(!isset($args[0])){
			$args[0] = "";
		}

		switch(strtolower($args[0])){
			case "reload":
				$sender->sendMessage(TF::GREEN . Translation::get(Translation::COMMANDS_BLOCKSNIPER_RELOAD));
				$this->loader->reload();

				return;

			case "menu":
			case "window":
				if(!$sender instanceof Player){
					$this->sendConsoleError($sender);

					return;
				}
				$sender->sendForm(new MainMenuWindow($this->loader, $sender));

				return;

			case "config":
				if(!$sender instanceof Player){
					$this->sendConsoleError($sender);

					return;
				}
				$sender->sendForm(new ConfigurationMenuWindow($this->loader));

				return;

			default:
				$sender->sendMessage($this->info);
		}
	}
}
