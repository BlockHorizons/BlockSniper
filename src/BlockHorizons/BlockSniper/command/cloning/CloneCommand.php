<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\command\cloning;

use BlockHorizons\BlockSniper\brush\shape\CuboidShape;
use BlockHorizons\BlockSniper\brush\Target;
use BlockHorizons\BlockSniper\command\BaseCommand;
use BlockHorizons\BlockSniper\data\Translation;
use BlockHorizons\BlockSniper\Loader;
use BlockHorizons\BlockSniper\session\SessionManager;
use BlockHorizons\libschematic\Schematic;
use pocketmine\command\CommandSender;
use pocketmine\math\Vector3;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use pocketmine\utils\TextFormat as TF;
use function strtolower;

class CloneCommand extends BaseCommand{

	public function __construct(Loader $loader){
		parent::__construct($loader, "clone", Translation::COMMANDS_CLONE_DESCRIPTION, "/clone <copy|schematic> [name]");
	}

	public function onExecute(CommandSender $sender, string $commandLabel, array $args) : void{
		/** @var Player $sender */
		if(!isset($args[0])){
			$sender->sendMessage($this->getUsage());

			return;
		}
		$session = SessionManager::getPlayerSession($sender);

		if(!$session->getSelection()->ready()){
			$sender->sendMessage(
				TextFormat::RED . Translation::get(Translation::COMMANDS_COMMON_WARNING_PREFIX) .
				Translation::get(Translation::BRUSH_SELECTION_ERROR)
			);

			return;
		}
		$shape = new CuboidShape($session->getBrush(), new Target(new Vector3(0, 0, 0), $sender->getWorld()), $session->getSelection()->box());

		switch(strtolower($args[0])){
			default:
			case "copy":
				if(!$sender->hasPermission("blocksniper.clone.copy")){
					return;
				}
				$session->getCloneStore()->saveCopy($shape, $session->getSelection()->getBottomCentre());
				$sender->sendMessage(TF::GREEN . Translation::get(Translation::COMMANDS_CLONE_COPY_SUCCESS));

				return;
			case "schematic":
				if(!$sender->hasPermission("blocksniper.clone.schematic")){
					return;
				}
				if(!isset($args[1])){
					$sender->sendMessage($this->getWarning() . Translation::get(Translation::COMMANDS_CLONE_SCHEMATIC_MISSING_NAME));

					return;
				}
				$path = $this->loader->getDataFolder() . "schematics/" . $args[1] . ".schematic";

				$schematic = new Schematic();
				$schematic->setBlocks($session->getSelection()->box(), $shape->getBlocks($sender->getWorld()));
				$schematic->save($path);

				$sender->sendMessage(TF::GREEN . Translation::get(Translation::COMMANDS_CLONE_SCHEMATIC_SUCCESS, $path));
		}
	}
}
