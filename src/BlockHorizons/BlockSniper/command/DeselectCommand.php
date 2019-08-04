<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\commands;

use BlockHorizons\BlockSniper\data\Translation;
use BlockHorizons\BlockSniper\Loader;
use BlockHorizons\BlockSniper\sessions\SessionManager;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\utils\TextFormat as TF;

class DeselectCommand extends BaseCommand{

	public function __construct(Loader $loader){
		parent::__construct($loader, "deselect", Translation::COMMANDS_DESELECT_DESCRIPTION, "/deselect");
	}

	public function onExecute(CommandSender $sender, string $commandLabel, array $args) : void{
		/**@var Player $sender */
		SessionManager::getPlayerSession($sender)->getSelection()->clear();
		$sender->sendMessage(TF::GREEN . Translation::get(Translation::COMMANDS_DESELECT_SUCCESS));
	}
}
