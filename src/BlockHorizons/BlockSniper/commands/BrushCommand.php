<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockSniper\commands;

use BlockHorizons\BlockSniper\Loader;
use BlockHorizons\BlockSniper\user_interface\WindowHandler;
use pocketmine\command\CommandSender;
use pocketmine\network\mcpe\protocol\ModalFormRequestPacket;
use pocketmine\Player;

class BrushCommand extends BaseCommand {

	public function __construct(Loader $loader) {
		parent::__construct($loader, "brush", "Change the properties of the brush", "/brush <parameter> <args>", ["b"]);
	}

	public function execute(CommandSender $sender, string $commandLabel, array $args): bool {
		if(!$this->testPermission($sender)) {
			$this->sendNoPermission($sender);
			return true;
		}

		if(!$sender instanceof Player) {
			$this->sendConsoleError($sender);
			return true;
		}

		$this->getLoader()->getBrushManager()->createBrush($sender);

		$windowHandler = new WindowHandler();
		$packet = new ModalFormRequestPacket();
		$packet->formId = $windowHandler->getWindowIdFor(WindowHandler::WINDOW_MAIN_MENU);
		$packet->formData = $windowHandler->getWindowJson(WindowHandler::WINDOW_MAIN_MENU);
		$sender->dataPacket($packet);
		return true;
	}
}
