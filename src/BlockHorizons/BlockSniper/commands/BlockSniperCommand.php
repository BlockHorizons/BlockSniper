<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockSniper\commands;

use BlockHorizons\BlockSniper\data\Translation;
use BlockHorizons\BlockSniper\Loader;
use BlockHorizons\BlockSniper\ui\WindowHandler;
use pocketmine\command\CommandSender;
use pocketmine\network\mcpe\protocol\ModalFormRequestPacket;
use pocketmine\Player;
use pocketmine\utils\TextFormat as TF;

class BlockSniperCommand extends BaseCommand {

	public function __construct(Loader $loader) {
		parent::__construct($loader, "blocksniper", (new Translation(Translation::COMMANDS_BLOCKSNIPER_DESCRIPTION))->getMessage(), "/blocksniper [menu|reload]", ["bs"]);
	}

	public function execute(CommandSender $sender, string $commandLabel, array $args): bool {
		if(!$this->testPermission($sender)) {
			$this->sendNoPermission($sender);
			return false;
		}
		if(!isset($args[0])) {
			$args[0] = "info";
		}

		switch(strtolower($args[0])) {
			case "reload":
				$sender->sendMessage(TF::GREEN . (new Translation(Translation::COMMANDS_BLOCKSNIPER_RELOAD))->getMessage());
				$this->getLoader()->reload();
				return true;

			case "menu":
			case "window":
				if(!$sender instanceof Player) {
					$this->sendConsoleError($sender);
					return false;
				}
				$windowHandler = new WindowHandler();
				$packet = new ModalFormRequestPacket();
				$packet->formId = $windowHandler->getWindowIdFor(WindowHandler::WINDOW_MAIN_MENU);
				$packet->formData = $windowHandler->getWindowJson(WindowHandler::WINDOW_MAIN_MENU, $this->getLoader(), $sender);
				$sender->dataPacket($packet);
				return true;

			default:
				$sender->sendMessage(TF::AQUA . "[BlockSniper] " . (new Translation(Translation::COMMANDS_BLOCKSNIPER_INFO))->getMessage() . "\n" .
					TF::GREEN . (new Translation(Translation::COMMANDS_BLOCKSNIPER_VERSION))->getMessage() . TF::YELLOW . Loader::VERSION . "\n" .
					TF::GREEN . (new Translation(Translation::COMMANDS_BLOCKSNIPER_TARGET_API))->getMessage() . TF::YELLOW . Loader::API_TARGET . "\n" .
					TF::GREEN . (new Translation(Translation::COMMANDS_BLOCKSNIPER_ORGANISATION))->getMessage() . TF::YELLOW . "BlockHorizons (https://github.com/BlockHorizons/BlockSniper)\n" .
					TF::GREEN . (new Translation(Translation::COMMANDS_BLOCKSNIPER_AUTHORS))->getMessage() . TF::YELLOW . "Sandertv (@Sandertv), Chris-Prime (@PrimusLV)");
				return true;
		}
	}
}
