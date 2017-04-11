<?php

namespace BlockHorizons\BlockSniper\commands\cloning;

use BlockHorizons\BlockSniper\brush\BrushManager;
use BlockHorizons\BlockSniper\cloning\types\Copy;
use BlockHorizons\BlockSniper\cloning\types\Template;
use BlockHorizons\BlockSniper\commands\BaseCommand;
use BlockHorizons\BlockSniper\Loader;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\utils\TextFormat as TF;

class CloneCommand extends BaseCommand {
	
	public function __construct(Loader $loader) {
		parent::__construct($loader, "clone", "Clone the area you're watching", "<type> <radiusXheight> [name]", []);
		$this->setPermission("blocksniper.command.clone");
		$this->setUsage(TF::RED . "[Usage] /clone <type> <radiusXheight> [name]");
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
		
		if(count($args) < 2 || count($args) > 3) {
			$sender->sendMessage($this->getUsage());
			return true;
		}
		
		$sizes = explode("x", strtolower($args[1]));
		
		if((int)$sizes[0] > $this->getSettings()->get("Maximum-Clone-Size") || (int)$sizes[1] > $this->getSettings()->get("Maximum-Clone-Size")) {
			$sender->sendMessage(TF::RED . "[Warning] " . $this->getLoader()->getTranslation("commands.errors.radius-too-big"));
			return true;
		}
		
		$center = $sender->getTargetBlock(100);
		if(!$center) {
			$sender->sendMessage(TF::RED . "[Warning] " . $this->getLoader()->getTranslation("commands.errors.no-target-found"));
			return true;
		}
		
		switch(strtolower($args[0])) {
			case "copy":
				$this->getLoader()->getBrushManager()->createBrush($sender);
				$shape = BrushManager::get($sender)->getShape();
				$cloneType = new Copy($this->getLoader()->getCloneStorer(), $sender->getLevel(), $this->getSettings()->get("Save-Air-In-Copy"), $center, $shape->getBlocksInside());
				break;
			
			case "template":
				if(!isset($args[2])) {
					$sender->sendMessage(TF::RED . "[Warning] " . $this->getLoader()->getTranslation("commands.errors.name-not-set"));
					return true;
				}
				$this->getLoader()->getBrushManager()->createBrush($sender);
				$shape = BrushManager::get($sender)->getShape();
				$cloneType = new Template($this->getLoader()->getCloneStorer(), $sender->getLevel(), $this->getSettings()->get("Save-Air-In-Copy"), $center, $shape->getBlocksInside(), $args[2]);
				break;

			case "schematic":
				// TODO: Implement Schematics
				return false;
			
			default:
				$sender->sendMessage(TF::RED . "[Warning] " . $this->getLoader()->getTranslation("commands.errors.clone-not-found"));
				return true;
		}
		$cloneType->saveClone();
		$sender->sendMessage(TF::GREEN . $this->getLoader()->getTranslation("commands.succeed.clone"));
	}
}
