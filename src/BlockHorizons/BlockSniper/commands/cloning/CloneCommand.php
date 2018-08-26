<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\commands\cloning;

use BlockHorizons\BlockSniper\cloning\types\CopyType;
use BlockHorizons\BlockSniper\cloning\types\TemplateType;
use BlockHorizons\BlockSniper\commands\BaseCommand;
use BlockHorizons\BlockSniper\data\Translation;
use BlockHorizons\BlockSniper\exceptions\InvalidBlockException;
use BlockHorizons\BlockSniper\Loader;
use BlockHorizons\BlockSniper\sessions\SessionManager;
use BlockHorizons\libschematic\Schematic;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\utils\TextFormat as TF;

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

		$center = $sender->getTargetBlock(100);
		if($center === null){
			throw new InvalidBlockException("No valid block could be found when attempting to clone.");
		}

		$size = SessionManager::getPlayerSession($sender)->getBrush()->size;
		switch(strtolower($args[0])){
			default:
			case "copy":
				$shape = SessionManager::getPlayerSession($sender)->getBrush()->getShape();
				$cloneType = new CopyType($sender, false, $center, $shape->getBlocksInside());
				$cloneType->saveClone();
				$sender->sendMessage(TF::GREEN . Translation::get(Translation::COMMANDS_CLONE_COPY_SUCCESS));

				return;

			case "template":
				if(!isset($args[1])){
					$sender->sendMessage($this->getWarning() . Translation::get(Translation::COMMANDS_CLONE_TEMPLATE_MISSING_NAME));

					return;
				}
				$shape = SessionManager::getPlayerSession($sender)->getBrush()->getShape();
				$cloneType = new TemplateType($sender, false, $center, $shape->getBlocksInside(), $args[1]);
				$cloneType->saveClone();
				$sender->sendMessage(TF::GREEN . Translation::get(Translation::COMMANDS_CLONE_TEMPLATE_SUCCESS, [$this->loader->getDataFolder() . "templates/" . $args[1] . ".template"]));

				return;

			case "scheme":
			case "schem":
			case "schematic":
				if(!isset($args[1])){
					$sender->sendMessage($this->getWarning() . Translation::get(Translation::COMMANDS_CLONE_SCHEMATIC_MISSING_NAME));

					return;
				}
				$shape = SessionManager::getPlayerSession($sender)->getBrush()->getShape();
				$schematic = new Schematic();
				$schematic
					->setBlocks($shape->getBlocksInside())
					->setMaterials(Schematic::MATERIALS_POCKET)
					->encode()
					->setLength($size * 2 + 1)
					->setHeight($size * 2 + 1)
					->setWidth($size * 2 + 1)
					->save($this->loader->getDataFolder() . "schematics/" . $args[1] . ".schematic");
				$sender->sendMessage(TF::GREEN . Translation::get(Translation::COMMANDS_CLONE_SCHEMATIC_SUCCESS, [$this->loader->getDataFolder() . "templates/" . $args[1] . ".schematic"]));
		}
	}
}
