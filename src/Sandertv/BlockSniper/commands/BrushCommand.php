<?php

namespace Sandertv\BlockSniper\commands;

use pocketmine\command\CommandSender;
use pocketmine\level\generator\biome\Biome;
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
			case "si":
				if(!is_numeric($args[1])) {
					$sender->sendMessage(TF::RED . "[Warning] " . $this->getPlugin()->getTranslation("commands.errors.radius-not-numeric"));
					return true;
				}
				Brush::setSize($sender, $args[1]);
				$sender->sendMessage(TF::GREEN . "Size: " . TF::AQUA . $args[1]);
				return true;
			
			case "sh":
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
						$sender->sendMessage(TF::GREEN . "Shape: " . TF::AQUA . Brush::getShape($sender)->getName());
						return true;
					
					default:
						$sender->sendMessage(TF::RED . "[Warning] " . $this->getPlugin()->getTranslation("commands.errors.shape-not-found"));
						return true;
				}
			
			case "ty":
			case "type":
				switch(strtolower($args[1])) {
					case "fill":
					case "clean":
					case "cleanentities":
					case "drain":
					case "flatten":
					case "layer":
					case "leafblower":
					case "overlay":
					case "replace":
					case "expand":
					case "melt":
					case "biome":
						if(!$sender->hasPermission("blocksniper.type." . $args[1])) {
							$sender->sendMessage(TF::RED . "[Warning] " . $this->getPlugin()->getTranslation("commands.errors.no-permission"));
							return true;
						}
						Brush::setType($sender, $args[1]);
						$sender->sendMessage(TF::GREEN . "Type: " . TF::AQUA . Brush::getType($sender)->getName());
						return true;
					
					default:
						$sender->sendMessage(TF::RED . "[Warning] " . $this->getPlugin()->getTranslation("commands.errors.shape-not-found"));
						return true;
				}
			
			case "he":
			case "height":
				if(!is_numeric($args[1])) {
					$sender->sendMessage(TF::RED . "[Warning] " . $this->getPlugin()->getTranslation("commands.errors.radius-not-numeric"));
					return true;
				}
				Brush::setHeight($sender, $args[1]);
				$sender->sendMessage(TF::GREEN . "Height: " . TF::AQUA . $args[1]);
				return true;
			
			case "bl":
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
			
			case "ob":
			case "obsolete":
			case "replaced":
				Brush::setObsolete($sender, $args[1]);
				$sender->sendMessage(TF::GREEN . "Obsolete: " . TF::AQUA . Brush::getObsolete($sender)->getName());
				return true;
			
			case "pe":
			case "perfect":
				Brush::setPerfect($sender, $args[1]);
				$sender->sendMessage(TF::GREEN . "Perfect: " . TF::AQUA . $args[1]);
				return true;
			
			case "gr":
			case "gravity":
				Brush::setGravity($sender, $args[1]);
				$sender->sendMessage(TF::GREEN . "Gravity: " . TF::AQUA . $args[1]);
				return true;
			
			case "decrement":
			case "decrementing":
			case "de":
				Brush::setDecrementing($sender, $args[1]);
				Brush::$resetSize[$sender->getId()] = Brush::getSize($sender);
				$sender->sendMessage(TF::GREEN . "Decrement: " . TF::AQUA . $args[1]);
				return true;
			
			case "bi":
			case "biome":
				$biome = array_slice($args, 1);
				Brush::setBiome($sender, implode(" ", $biome));
				$sender->sendMessage(TF::GREEN . "Biome: " . TF::AQUA . Biome::getBiome(Brush::getBiomeIdFromString($sender))->getName());
				return true;
			
			default:
				$sender->sendMessage(TF::RED . "[Usage] /brush <size|shape|type|blocks|height|obsolete|perfect> <value>");
				return true;
		}
	}
}
