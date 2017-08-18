<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockSniper\commands;

use BlockHorizons\BlockSniper\data\Translation;
use BlockHorizons\BlockSniper\Loader;
use BlockHorizons\BlockSniper\ui\WindowHandler;
use pocketmine\command\CommandSender;
use pocketmine\network\mcpe\protocol\ModalFormRequestPacket;
use pocketmine\Player;

class BrushCommand extends BaseCommand {

	public function __construct(Loader $loader) {
		parent::__construct($loader, "brush", (new Translation(Translation::COMMANDS_BRUSH_DESCRIPTION))->getMessage(), "/brush", ["b"]);
	}

	public function execute(CommandSender $sender, string $commandLabel, array $args): bool {
		if(!$this->testPermission($sender)) {
			$this->sendNoPermission($sender);
			return false;
		}

		if(!$sender instanceof Player) {
			$this->sendConsoleError($sender);
			return false;
		}

		$this->getLoader()->getBrushManager()->createBrush($sender);

		$windowHandler = new WindowHandler();
		$packet = new ModalFormRequestPacket();
		$packet->formId = $windowHandler->getWindowIdFor(WindowHandler::WINDOW_MAIN_MENU);
		$packet->formData = $windowHandler->getWindowJson(WindowHandler::WINDOW_MAIN_MENU, $this->getLoader(), $sender);
		$sender->dataPacket($packet);
		return true;
	}
}
