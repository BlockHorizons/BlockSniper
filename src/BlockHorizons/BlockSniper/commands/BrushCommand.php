<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockSniper\commands;

use BlockHorizons\BlockSniper\data\Translation;
use BlockHorizons\BlockSniper\Loader;
use BlockHorizons\BlockSniper\ui\WindowHandler;
use BlockHorizons\BlockSniper\ui\windows\MainMenuWindow;
use pocketmine\command\CommandSender;
use pocketmine\Player;

class BrushCommand extends BaseCommand {

	public function __construct(Loader $loader) {
		parent::__construct($loader, "brush", Translation::COMMANDS_BRUSH_DESCRIPTION, "/brush", ["b"]);
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
		$sender->sendForm(new MainMenuWindow($this->loader, $sender));
		return true;
	}
}
