<?php

namespace BlockHorizons\BlockSniper\commands\cloning;

use BlockHorizons\BlockSniper\brush\BrushManager;
use BlockHorizons\BlockSniper\brush\shapes\CubeShape;
use BlockHorizons\BlockSniper\brush\shapes\CuboidShape;
use BlockHorizons\BlockSniper\brush\shapes\CylinderShape;
use BlockHorizons\BlockSniper\brush\shapes\SphereShape;
use BlockHorizons\BlockSniper\cloning\types\CopyType;
use BlockHorizons\BlockSniper\cloning\types\SchematicType;
use BlockHorizons\BlockSniper\cloning\types\TemplateType;
use BlockHorizons\BlockSniper\commands\BaseCommand;
use BlockHorizons\BlockSniper\Loader;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\utils\TextFormat as TF;

class CloneCommand extends BaseCommand {
	
	public function __construct(Loader $loader) {
		parent::__construct($loader, "clone", "Clone the area you're watching", "<type> [name]", []);
		$this->setPermission("blocksniper.command.clone");
		$this->setUsage(TF::RED . "[Usage] /clone <type> [name]");
	}
	
	public function execute(CommandSender $sender, $commandLabel, array $args) {
		if(!$this->testPermission($sender)) {
			$this->sendNoPermission($sender);
			return true;
		}
		
		if(!$sender instanceof Player) {
			$this->sendConsoleError($sender);
			return true;
		}
		
		if(count($args) < 1 || count($args) > 2) {
			$sender->sendMessage($this->getUsage());
			return true;
		}
		
		$center = $sender->getTargetBlock(100);
		if(!$center) {
			$sender->sendMessage(TF::RED . "[Warning] " . $this->getLoader()->getTranslation("commands.errors.no-target-found"));
			return true;
		}

		$this->getLoader()->getBrushManager()->createBrush($sender);
		switch(strtolower($args[0])) {
			case "copy":
				$shape = BrushManager::get($sender)->getShape(true);
				$cloneType = new CopyType($this->getLoader()->getCloneStorer(), $sender->getLevel(), $this->getSettings()->get("Save-Air-In-Copy"), $center, $shape->getBlocksInside());
				break;
			
			case "template":
				if(!isset($args[1])) {
					$sender->sendMessage(TF::RED . "[Warning] " . $this->getLoader()->getTranslation("commands.errors.name-not-set"));
					return true;
				}
				$shape = BrushManager::get($sender)->getShape(true);
				$cloneType = new TemplateType($this->getLoader()->getCloneStorer(), $sender->getLevel(), $this->getSettings()->get("Save-Air-In-Copy"), $center, $shape->getBlocksInside(), $args[1]);
				break;

			case "schematic":
				if(!isset($args[1])) {
					$sender->sendMessage(TF::RED . "[Warning] " .  $this->getLoader()->getTranslation("commands.errors.name-not-set"));
					return true;
				}
				$shape = BrushManager::get($sender)->getShape(true);

				if($shape instanceof CylinderShape || $shape instanceof CuboidShape) {
					$height = $shape->getHeight() * 2 + 1;
				} elseif($shape instanceof SphereShape) {
					$height = $shape->getRadius() * 2 + 1;
				} else {
					$height = $shape->getWidth() * 2 + 1;
				}

				if($shape instanceof CuboidShape || $shape instanceof CubeShape) {
					$length = $shape->getWidth() * 2 + 1;
					$width = $length;
				} else {
					$length = $shape->getRadius() * 2 + 1;
					$width = $length;
				}
				$this->getLoader()->getSchematicProcessor()->submitValues($shape->getBlocksInside(), $length, $width, $height);
				$cloneType = new SchematicType($this->getLoader()->getCloneStorer(), $sender->getLevel(), $this->getSettings()->get("Save-Air-In-Copy"), $center, $shape->getBlocksInside(), $args[1]);
				break;
			
			default:
				$sender->sendMessage(TF::RED . "[Warning] " . $this->getLoader()->getTranslation("commands.errors.clone-not-found"));
				return true;
		}
		$cloneType->saveClone();
		$sender->sendMessage(TF::GREEN . $this->getLoader()->getTranslation("commands.succeed.clone"));
		return true;
	}
}
