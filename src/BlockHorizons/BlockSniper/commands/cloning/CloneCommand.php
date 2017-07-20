<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockSniper\commands\cloning;

use BlockHorizons\BlockSniper\brush\BrushManager;
use BlockHorizons\BlockSniper\cloning\types\CopyType;
use BlockHorizons\BlockSniper\cloning\types\TemplateType;
use BlockHorizons\BlockSniper\commands\BaseCommand;
use BlockHorizons\BlockSniper\Loader;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\utils\TextFormat as TF;
use libschematic\Schematic;

class CloneCommand extends BaseCommand {
	
	public function __construct(Loader $loader) {
		parent::__construct($loader, "clone", "Clone the area you're watching", "/clone <type> [name]", []);
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
		
		$center = $sender->getTargetBlock(100);
		$this->getLoader()->getBrushManager()->createBrush($sender);
		switch(strtolower($args[0])) {
			default:
			case "copy":
				$shape = BrushManager::get($sender)->getShape(true, BrushManager::get($sender)->getYOffset());
				$cloneType = new CopyType($this->getLoader()->getCloneStorer(), $sender, $this->getSettings()->saveAirInCopy(), $center, $shape->getBlocksInside());
				break;
			
			case "template":
				if(!isset($args[1])) {
					$sender->sendMessage(TF::RED . "[Warning] " . $this->getLoader()->getTranslation("commands.errors.name-not-set"));
					return true;
				}
				$shape = BrushManager::get($sender)->getShape(true, BrushManager::get($sender)->getYOffset());
				$cloneType = new TemplateType($this->getLoader()->getCloneStorer(), $sender, $this->getSettings()->saveAirInCopy(), $center, $shape->getBlocksInside(), $args[1]);
				break;

			case "schematic":
				if(!isset($args[1])) {
					$sender->sendMessage(TF::RED . "[Warning] " .  $this->getLoader()->getTranslation("commands.errors.name-not-set"));
					return true;
				}
				$shape = BrushManager::get($sender)->getShape(true, BrushManager::get($sender)->getYOffset());

				new Schematic()
					->setBlocks($shape->getBlocksInside())
					->setMaterials(Schematic::MATERIALS_ALPHA)
					->encode()
					->save($this->getLoader()->getDataFolder() . "schematics/" . $args[1] . ".schematic");

				$sender->sendMessage(TF::GREEN . $this->getLoader()->getTranslation("commands.succeed.clone"));
				return true;

			case "offset":
			case "yoffset":
				if(!isset($args[1])) {
					$offset = 0;
				} elseif(is_numeric($args[1])) {
					$offset = $args[1];
				} else {
					$offset = 0;
				}
				BrushManager::get($sender)->setYOffset($offset);
				$sender->sendMessage(TF::GREEN . $this->getLoader()->getTranslation("brush.yoffset"));
				return true;
		}
		$cloneType->saveClone();
		$sender->sendMessage(TF::GREEN . $this->getLoader()->getTranslation("commands.succeed.clone"));
		return true;
	}
}
