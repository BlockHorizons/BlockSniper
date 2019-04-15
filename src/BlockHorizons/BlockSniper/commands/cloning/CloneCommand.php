<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\commands\cloning;

use BlockHorizons\BlockSniper\cloning\types\CopyType;
use BlockHorizons\BlockSniper\cloning\types\TemplateType;
use BlockHorizons\BlockSniper\commands\BaseCommand;
use BlockHorizons\BlockSniper\data\Translation;
use BlockHorizons\BlockSniper\exceptions\InvalidItemException;
use BlockHorizons\BlockSniper\Loader;
use BlockHorizons\BlockSniper\sessions\SessionManager;
use BlockHorizons\libschematic\Schematic;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\utils\TextFormat;
use pocketmine\utils\TextFormat as TF;
use function strtolower;

class CloneCommand extends BaseCommand{

	public function __construct(Loader $loader){
		parent::__construct($loader, "clone", Translation::COMMANDS_CLONE_DESCRIPTION, "/clone <copy|schematic|template> [name]");
	}

	public function onExecute(CommandSender $sender, string $commandLabel, array $args) : void{
		/** @var Player $sender */
		if(!isset($args[0])){
			$sender->sendMessage($this->getUsage());

			return;
		}

		$center = $sender->getTargetBlock($sender->getViewDistance() * 16);
		if($center === null){
			throw new InvalidItemException("No valid block could be found when attempting to clone.");
		}

		$session = SessionManager::getPlayerSession($sender);

		if(!$session->getSelection()->ready()){
			$sender->sendMessage(
				TextFormat::RED . Translation::get(Translation::COMMANDS_COMMON_WARNING_PREFIX) .
				Translation::get(Translation::BRUSH_SELECTION_ERROR)
			);

			return;
		}

		$shape = $session->getBrush()->getShape($session->getSelection()->box());

		switch(strtolower($args[0])){
			default:
			case "copy":
				$cloneType = new CopyType($sender, false, $shape);
				$cloneType->saveClone();
				$sender->sendMessage(TF::GREEN . Translation::get(Translation::COMMANDS_CLONE_COPY_SUCCESS));

				return;

			case "template":
				if(!isset($args[1])){
					$sender->sendMessage($this->getWarning() . Translation::get(Translation::COMMANDS_CLONE_TEMPLATE_MISSING_NAME));

					return;
				}
				$cloneType = new TemplateType($sender, false, $center, $shape, $args[1]);
				$cloneType->saveClone();
				$sender->sendMessage(TF::GREEN . Translation::get(Translation::COMMANDS_CLONE_TEMPLATE_SUCCESS, $this->loader->getDataFolder() . "templates/" . $args[1] . ".template"));

				return;

			case "scheme":
			case "schem":
			case "schematic":
				if(!isset($args[1])){
					$sender->sendMessage($this->getWarning() . Translation::get(Translation::COMMANDS_CLONE_SCHEMATIC_MISSING_NAME));

					return;
				}
				$path = $this->loader->getDataFolder() . "schematics/" . $args[1] . ".schematic";

				$schematic = new Schematic();
				$schematic->setBlocks($session->getSelection()->box(), $shape->getBlocksInside());
				$schematic->save($path);

				$sender->sendMessage(TF::GREEN . Translation::get(Translation::COMMANDS_CLONE_SCHEMATIC_SUCCESS, $path));
		}
	}
}
