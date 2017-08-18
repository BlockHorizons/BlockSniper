<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockSniper\commands\cloning;

use BlockHorizons\BlockSniper\brush\BrushManager;
use BlockHorizons\BlockSniper\cloning\types\CopyType;
use BlockHorizons\BlockSniper\cloning\types\TemplateType;
use BlockHorizons\BlockSniper\commands\BaseCommand;
use BlockHorizons\BlockSniper\data\Translation;
use BlockHorizons\BlockSniper\Loader;
use libschematic\Schematic;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\utils\TextFormat as TF;

class CloneCommand extends BaseCommand {

	public function __construct(Loader $loader) {
		parent::__construct($loader, "clone", (new Translation(Translation::COMMANDS_CLONE_DESCRIPTION))->getMessage(), "/clone <type> [name]", []);
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

		$center = $sender->getTargetBlock(100);
		$this->getLoader()->getBrushManager()->createBrush($sender);
		$size = BrushManager::get($sender)->getSize();
		switch(strtolower($args[0])) {
			default:
			case "copy":
				$shape = BrushManager::get($sender)->getShape(true, BrushManager::get($sender)->getYOffset());
				$cloneType = new CopyType($this->getLoader()->getCloneStorer(), $sender, false, $center, $shape->getBlocksInside());
				$cloneType->saveClone();
				$sender->sendMessage(TF::GREEN . (new Translation(Translation::COMMANDS_CLONE_COPY_SUCCESS))->getMessage());
				return true;

			case "template":
				if(!isset($args[1])) {
					$sender->sendMessage($this->getWarning() . (new Translation(Translation::COMMANDS_CLONE_TEMPLATE_MISSING_NAME))->getMessage());
					return false;
				}
				$shape = BrushManager::get($sender)->getShape(true, BrushManager::get($sender)->getYOffset());
				$cloneType = new TemplateType($this->getLoader()->getCloneStorer(), $sender, false, $center, $shape->getBlocksInside(), $args[1]);
				$cloneType->saveClone();
				$sender->sendMessage(TF::GREEN . (new Translation(Translation::COMMANDS_CLONE_TEMPLATE_SUCCESS, [$this->getLoader()->getDataFolder() . "templates/" . $args[1]]))->getMessage());
				return true;

			case "scheme":
			case "schem":
			case "schematic":
				if(!isset($args[1])) {
					$sender->sendMessage($this->getWarning() . (new Translation(Translation::COMMANDS_CLONE_SCHEMATIC_MISSING_NAME))->getMessage());
					return false;
				}
				$shape = BrushManager::get($sender)->getShape(true, BrushManager::get($sender)->getYOffset());
				$schematic = new Schematic();
				$schematic
					->setBlocks($shape->getBlocksInside())
					->setMaterials(Schematic::MATERIALS_ALPHA)
					->encode()
					->setLength($size * 2 + 1)
					->setHeight($size * 2 + 1)
					->setWidth($size * 2 + 1)
					->save($this->getLoader()->getDataFolder() . "schematics/" . $args[1] . ".schematic");
				$sender->sendMessage(TF::GREEN . (new Translation(Translation::COMMANDS_CLONE_SCHEMATIC_SUCCESS, [$this->getLoader()->getDataFolder() . "templates/" . $args[1]]))->getMessage());
				return true;
		}
	}
}
