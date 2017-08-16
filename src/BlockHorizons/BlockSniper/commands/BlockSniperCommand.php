<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockSniper\commands;

use BlockHorizons\BlockSniper\Loader;
use BlockHorizons\BlockSniper\ui\WindowHandler;
use pocketmine\command\CommandSender;
use pocketmine\network\mcpe\protocol\ModalFormRequestPacket;
use pocketmine\Player;
use pocketmine\utils\TextFormat;
use pocketmine\utils\TextFormat as TF;

class BlockSniperCommand extends BaseCommand {

	public function __construct(Loader $loader) {
		parent::__construct($loader, "blocksniper", "Get information or change things related to BlockSniper", "/blocksniper [menu|reload]", ["bs"]);
	}

	public function execute(CommandSender $sender, string $commandLabel, array $args): bool {
		if(!$this->testPermission($sender)) {
			$this->sendNoPermission($sender);
		}

		if(!isset($args[0])) {
			$sender->sendMessage(TF::AQUA . "[BlockSniper] Information\n" .
				TF::GREEN . "Version: " . TF::YELLOW . Loader::VERSION . "\n" .
				TF::GREEN . "Target API: " . TF::YELLOW . Loader::API_TARGET . "\n" .
				TF::GREEN . "Organization: " . TF::YELLOW . "BlockHorizons (https://github.com/BlockHorizons/BlockSniper)\n" .
				TF::GREEN . "Authors: " . TF::YELLOW . "Sandertv (@Sandertv), Chris-Prime (@PrimusLV)");
			return true;
		}

		switch(strtolower($args[0])) {
			case "reload":
				$sender->sendMessage(TF::GREEN . "Reloading...");
				$this->getLoader()->reload();
				return true;

			case "menu":
			case "window":
				if(!$sender instanceof Player) {
					$sender->sendMessage(TextFormat::RED . "[Warning] This command can only be used in-game.");
					return true;
				}
				$this->getLoader()->getBrushManager()->createBrush($sender);

				$windowHandler = new WindowHandler();
				$packet = new ModalFormRequestPacket();
				$packet->formId = $windowHandler->getWindowIdFor(WindowHandler::WINDOW_MAIN_MENU);
				$packet->formData = $windowHandler->getWindowJson(WindowHandler::WINDOW_MAIN_MENU, $this->getLoader(), $sender);
				$sender->dataPacket($packet);
				return true;

			default:
				$sender->sendMessage(TF::AQUA . "[BlockSniper] Information\n" .
					TF::GREEN . "Version: " . TF::YELLOW . Loader::VERSION . "\n" .
					TF::GREEN . "Target API: " . TF::YELLOW . Loader::API_TARGET . "\n" .
					TF::GREEN . "Organization: " . TF::YELLOW . "BlockHorizons (https://github.com/BlockHorizons/BlockSniper)\n" .
					TF::GREEN . "Authors: " . TF::YELLOW . "Sandertv (@Sandertv), Chris-Prime (@PrimusLV)");
				return true;
		}
	}
}
