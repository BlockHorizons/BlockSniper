<?php

namespace Sandertv\BlockSniper\commands;

use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\utils\TextFormat as TF;
use Sandertv\BlockSniper\brush\Brush;
use Sandertv\BlockSniper\Loader;

class BrushCommand extends BaseCommand {
	
	public function __construct(Loader $owner) {
		parent::__construct($owner, "brush", "Change the properties of the brush", "<size|shape|type|blocks|height|obsolete|perfect> <args>", ["b", "brushwand"]);
		$this->setPermission("blocksniper.command.brush");
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
		
		if(count($args) !== 2) {
			$sender->sendMessage(TF::RED . "[Usage] /brush <size|shape|type|blocks|height|obsolete|perfect> <value>");
			return true;
		}
		
		Brush::setupDefaultValues($sender);
		
		switch(strtolower($args[0])) {
			case "size":
			case "radius":
				if(!is_numeric($args[1])) {
					$sender->sendMessage(TF::RED . "[Warning] " . $this->getPlugin()->getTranslation("commands.errors.radius-not-numeric"));
					return true;
				}
				Brush::setSize($sender, $args[1]);
				$sender->sendMessage(TF::GREEN . "Size: " . TF::AQUA . $args[1]);
				return true;
			
			case "shape":
				switch(strtolower($args[1])) {
					case "cube":
					case "sphere":
					case "cuboid":
					case "cylinder":
						if(!$sender->hasPermission("blocksniper.shape." . $args[1])) {
							$sender->sendMessage(TF::RED . "[Warning] " . $this->getPlugin()->getTranslation("commands.errors.no-permission"));
							return true;
						}
						Brush::setShape($sender, $args[1]);
						$sender->sendMessage(TF::GREEN . "Shape: " . TF::AQUA . $args[1]);
						return true;
					
					default:
						$sender->sendMessage(TF::RED . "[Warning] " . $this->getPlugin()->getTranslation("commands.errors.shape-not-found"));
						return true;
				}
			
			case "type":
				switch(strtolower($args[1])) {
					case "fill":
					case "clean":
					case "drain":
					case "flatten":
					case "layer":
					case "leafblower":
					case "overlay":
					case "replace":
					case "expand":
					case "melt":
						if(!$sender->hasPermission("blocksniper.type." . $args[1])) {
							$sender->sendMessage(TF::RED . "[Warning] " . $this->getPlugin()->getTranslation("commands.errors.no-permission"));
							return true;
						}
						Brush::setType($sender, $args[1]);
						$sender->sendMessage(TF::GREEN . "Type: " . TF::AQUA . $args[1]);
						return true;
					
					default:
						$sender->sendMessage(TF::RED . "[Warning] " . $this->getPlugin()->getTranslation("commands.errors.shape-not-found"));
						return true;
				}
			
			case "height":
				if(!is_numeric($args[1])) {
					$sender->sendMessage(TF::RED . "[Warning] " . $this->getPlugin()->getTranslation("commands.errors.radius-not-numeric"));
					return true;
				}
				Brush::setHeight($sender, $args[1]);
				$sender->sendMessage(TF::GREEN . "Height: " . TF::AQUA . $args[1]);
				return true;
			
			case "block":
			case "blocks":
				$blocks = explode(",", $args[1]);
				Brush::setBlocks($sender, $blocks);
				$blocks = Brush::getBlocks($sender);
				$blockNames = [];
				foreach($blocks as $block) {
					$blockNames[] = $block->getName();
				}
				$sender->sendMessage(TF::GREEN . "Blocks: " . TF::AQUA . implode(", ", $blockNames));
				return true;
			
			case "obsolete":
			case "replaced":
				Brush::setObsolete($sender, $args[1]);
				$sender->sendMessage(TF::GREEN . "Obsolete: " . TF::AQUA . Brush::getObsolete($sender)->getName());
				return true;
			
			case "perfect":
				Brush::setPerfect($sender, $args[1]);
				$sender->sendMessage(TF::GREEN . "Perfect: " . TF::AQUA . $args[1]);
				return true;
				
			default:
				$sender->sendMessage(TF::RED . "[Usage] /brush <size|shape|type|blocks|height|obsolete|perfect> <value>");
				return true;
		}
	}
}
