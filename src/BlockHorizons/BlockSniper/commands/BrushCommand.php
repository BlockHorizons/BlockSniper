<?php

namespace BlockHorizons\BlockSniper\commands;

use BlockHorizons\BlockSniper\brush\BaseShape;
use BlockHorizons\BlockSniper\brush\BaseType;
use BlockHorizons\BlockSniper\brush\BrushManager;
use BlockHorizons\BlockSniper\events\ChangeBrushPropertiesEvent as Change;
use BlockHorizons\BlockSniper\Loader;
use pocketmine\command\CommandSender;
use pocketmine\level\generator\biome\Biome;
use pocketmine\Player;
use pocketmine\utils\TextFormat as TF;

class BrushCommand extends BaseCommand {
	
	public function __construct(Loader $loader) {
		parent::__construct($loader, "brush", "Change the properties of the brush", "<parameter> <args>", ["b", "brushwand"]);
		$this->setPermission("blocksniper.command.brush");
		$this->setUsage(TF::RED . "[Usage] /brush <parameter> <value>");
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
		
		if(count($args) !== 2 && strtolower($args[0]) !== "reset" && strtolower($args[0]) !== "re" && strtolower($args[1]) !== "delete") {
			$sender->sendMessage($this->getUsage());
			return true;
		}
		
		$this->getLoader()->getBrushManager()->createBrush($sender);
		$brush = BrushManager::get($sender);
		
		$action = null;
		
		switch(strtolower($args[0])) {
			case "preset":
			case "pr":
				switch($args[1]) {
					case "new":
					case "create":
						if($this->getLoader()->getPresetManager()->isPreset($args[1])) {
							$sender->sendMessage(TF::RED . "[Warning] " . $this->getLoader()->getTranslation("commands.errors.preset-already-exists"));
							return true;
						}
						$this->getLoader()->getPresetManager()->presetCreation[$sender->getId()] = [];
						$sender->sendMessage(TF::GREEN . $this->getLoader()->getTranslation("commands.succeed.preset.name"));
						$sender->sendMessage(TF::GREEN . $this->getLoader()->getTranslation("commands.succeed.preset.cancel"));
						return true;
					
					case "list":
						$presetList = implode(", ", $this->getLoader()->getPresetManager()->getAllPresets());
						$sender->sendMessage(TF::GREEN . "--- " . TF::YELLOW . "Preset List" . TF::GREEN . " ---");
						$sender->sendMessage(TF::AQUA . $presetList);
						return true;
					
					case "delete":
						if(!$this->getLoader()->getPresetManager()->isPreset($args[2])) {
							$sender->sendMessage(TF::RED . "[Warning] " . $this->getLoader()->getTranslation("commands.errors.preset-doesnt-exist"));
							return true;
						}
						$this->getLoader()->getPresetManager()->deletePreset($args[2]);
						$sender->sendMessage(TF::YELLOW . "Preset " . TF::RED . $args[2] . TF::YELLOW . " has been deleted successfully.");
						return true;
						
					default:
						if(!$this->getLoader()->getPresetManager()->isPreset($args[1])) {
							$sender->sendMessage(TF::RED . "[Warning] " . $this->getLoader()->getTranslation("commands.errors.preset-doesnt-exist"));
							return true;
						}
						$preset = $this->getLoader()->getPresetManager()->getPreset($args[1]);
						$preset->apply($sender);
						$sender->sendMessage(TF::YELLOW . $this->getLoader()->getTranslation("brush.preset") . TF::BLUE . $preset->name);
						foreach($preset->getParsedData() as $key => $value) {
							if($value !== null && $key !== "name") {
								if(is_array($value)) {
									$sender->sendMessage(TF::GREEN . $this->getLoader()->getTranslation("brush" . $key) . TF::AQUA . implode(", ", $value));
								} else {
									$sender->sendMessage(TF::GREEN . $this->getLoader()->getTranslation("brush." . $key) . TF::AQUA . $value);
								}
							}
						}
						return true;
				}
				break;
				
			case "size":
			case "radius":
			case "si":
				if(!is_numeric($args[1])) {
					$sender->sendMessage(TF::RED . "[Warning] " . $this->getLoader()->getTranslation("commands.errors.radius-not-numeric"));
					return true;
				}
				if($args[1] > $this->getLoader()->getSettings()->get("Maximum-Radius")) {
					$sender->sendMessage(TF::RED . "[Warning] " . $this->getLoader()->getTranslation("commands.errors.radius-too-big"));
					return true;
				}
				$brush->setSize($args[1]);
				$sender->sendMessage(TF::GREEN . $this->getLoader()->getTranslation("brush.size") . TF::AQUA . $args[1]);
				$action = Change::ACTION_CHANGE_SIZE;
				break;
			
			case "sh":
			case "shape":
				if(!BaseShape::isShape($args[1])) {
					$sender->sendMessage(TF::RED . "[Warning] " . $this->getLoader()->getTranslation("commands.errors.shape-not-found"));
					return true;
				}
				if(!$sender->hasPermission("blocksniper.shape." . $args[1])) {
					$sender->sendMessage(TF::RED . "[Warning] " . $this->getLoader()->getTranslation("commands.errors.no-permission"));
					return true;
				}
				$brush->setShape($args[1]);
				$sender->sendMessage(TF::GREEN . $this->getLoader()->getTranslation("brush.shape") . TF::AQUA . $brush->getShape()->getName());
				$action = Change::ACTION_CHANGE_SHAPE;
				break;
			
			case "ty":
			case "type":
				if(!BaseType::isType($args[1])) {
					$sender->sendMessage(TF::RED . "[Warning] " . $this->getLoader()->getTranslation("commands.errors.shape-not-found"));
					return true;
				}
				if(!$sender->hasPermission("blocksniper.type." . $args[1])) {
					$sender->sendMessage(TF::RED . "[Warning] " . $this->getLoader()->getTranslation("commands.errors.no-permission"));
					return true;
				}
				$brush->setType($args[1]);
				$sender->sendMessage(TF::GREEN . $this->getLoader()->getTranslation("brush.type") . TF::AQUA . $brush->getType()->getName());
				$action = Change::ACTION_CHANGE_TYPE;
				break;
			
			case "he":
			case "height":
				if(!is_numeric($args[1])) {
					$sender->sendMessage(TF::RED . "[Warning] " . $this->getLoader()->getTranslation("commands.errors.radius-not-numeric"));
					return true;
				}
				$brush->setHeight($args[1]);
				$sender->sendMessage(TF::GREEN . $this->getLoader()->getTranslation("brush.height") . TF::AQUA . $args[1]);
				$action = Change::ACTION_CHANGE_HEIGHT;
				break;
			
			case "bl":
			case "block":
			case "blocks":
				$blocks = explode(",", $args[1]);
				$brush->setBlocks($blocks);
				$blocks = $brush->getBlocks();
				$blockNames = [];
				foreach($blocks as $block) {
					$blockNames[] = $block->getName();
				}
				$sender->sendMessage(TF::GREEN . $this->getLoader()->getTranslation("brush.blocks") . TF::AQUA . implode(", ", $blockNames));
				$action = Change::ACTION_CHANGE_BLOCKS;
				break;
			
			case "ob":
			case "obsolete":
			case "replaced":
				$blocks = explode(",", $args[1]);
				$brush->setObsolete($blocks);
				$blocks = $brush->getObsolete();
				$blockNames = [];
				foreach($blocks as $block) {
					$blockNames[] = $block->getName();
				}
				$sender->sendMessage(TF::GREEN . $this->getLoader()->getTranslation("brush.obsolete") . TF::AQUA . implode(", ", $blockNames));
				$action = Change::ACTION_CHANGE_OBSOLETE;
				break;
			
			case "pe":
			case "perfect":
				$brush->setPerfect($args[1]);
				$sender->sendMessage(TF::GREEN . $this->getLoader()->getTranslation("brush.perfect") . TF::AQUA . (string)$brush->getPerfect());
				return true;
			
			case "decrement":
			case "decrementing":
			case "de":
				$brush->setDecrementing($args[1]);
				$brush->resetSize[$sender->getId()] = $brush->getSize();
				$sender->sendMessage(TF::GREEN . $this->getLoader()->getTranslation("brush.decrement") . TF::AQUA . (string)$brush->isDecrementing());
				$action = Change::ACTION_CHANGE_DECREMENT;
				break;
			
			case "bi":
			case "biome":
				$biome = array_slice($args, 1);
				$brush->setBiome(implode(" ", $biome));
				$sender->sendMessage(TF::GREEN . $this->getLoader()->getTranslation("brush.biome") . TF::AQUA . Biome::getBiome($brush->getBiomeId())->getName());
				$action = Change::ACTION_CHANGE_BIOME;
				break;
				
			case "re":
			case "reset":
				$this->getLoader()->getBrushManager()->resetBrush($sender);
				$sender->sendMessage(TF::GREEN . $this->getLoader()->getTranslation("commands.succeed.brush.reset"));
				$action = Change::ACTION_RESET_BRUSH;
				break;
			
			case "ho":
			case "hollow":
				$brush->setHollow($args[1]);
				$sender->sendMessage(TF::GREEN . $this->getLoader()->getTranslation("brush.hollow") . TF::AQUA . (string)$brush->getHollow());
				$action = Change::ACTION_CHANGE_HOLLOW;
				break;
			
			case "tr":
			case "tree":
				$brush->setTree($args[1]);
				$sender->sendMessage(TF::GREEN . $this->getLoader()->getTranslation("brush.tree") . TF::AQUA . $brush->getTreeType());
				$action = Change::ACTION_CHANGE_TREE;
				break;
				
			default:
				$sender->sendMessage(TF::RED . "[Usage] /brush <parameter> <value>");
				return true;
			
		}
		$this->getLoader()->getServer()->getPluginManager()->callEvent(new Change($this->getLoader(), $sender, $action, $args[0]));
		return true;
	}
}
