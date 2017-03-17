<?php

namespace Sandertv\BlockSniper\commands;

use pocketmine\command\CommandSender;
use pocketmine\level\generator\biome\Biome;
use pocketmine\Player;
use pocketmine\utils\TextFormat as TF;
use Sandertv\BlockSniper\brush\BaseShape;
use Sandertv\BlockSniper\brush\BaseType;
use Sandertv\BlockSniper\brush\Brush;
use Sandertv\BlockSniper\events\ChangeBrushPropertiesEvent as Change;
use Sandertv\BlockSniper\Loader;

class BrushCommand extends BaseCommand {
	
	public function __construct(Loader $owner) {
		parent::__construct($owner, "brush", "Change the properties of the brush", "<parameter> <args>", ["b", "brushwand"]);
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
		
		if(count($args) !== 2 && strtolower($args[0]) !== "reset"  && strtolower($args[0]) !== "re") {
			$sender->sendMessage(TF::RED . "[Usage] /brush <parameter> <value>");
			return true;
		}
		
		Brush::setupDefaultValues($sender);
		$action = null;
		
		switch(strtolower($args[0])) {
			case "preset":
			case "pr":
				switch($args[1]) {
					case "new":
						if($this->getPlugin()->getPresetManager()->isPreset(strtolower($args[1]))) {
							$sender->sendMessage(TF::RED . "[Warning] " . $this->getPlugin()->getTranslation("commands.errors.preset-already-exists"));
							return true;
						}
						$this->getPlugin()->getPresetManager()->presetCreation[$sender->getId()] = [];
						$sender->sendMessage(TF::GREEN . $this->getPlugin()->getTranslation("commands.succeed.preset.name"));
						$sender->sendMessage(TF::GREEN . $this->getPlugin()->getTranslation("commands.succeed.preset.cancel"));
						return true;
					
					default:
						if(!$this->getPlugin()->getPresetManager()->isPreset(strtolower($args[1]))) {
							$sender->sendMessage(TF::RED . "[Warning] " . $this->getPlugin()->getTranslation("commands.errors.preset-doesnt-exist"));
							return true;
						}
						$preset = $this->getPlugin()->getPresetManager()->getPreset(strtolower($args[1]));
						$preset->apply($sender);
						$sender->sendMessage(TF::YELLOW . $this->getPlugin()->getTranslation("brush.preset") . TF::BLUE . $preset->name);
						foreach($preset->getParsedData() as $key => $value) {
							if($value !== null) {
								$sender->sendMessage(TF::GREEN . $this->getPlugin()->getTranslation("brush." . $key) . TF::AQUA . $value);
							}
						}
						return true;
				}
				break;
				
			case "size":
			case "radius":
			case "si":
				if(!is_numeric($args[1])) {
					$sender->sendMessage(TF::RED . "[Warning] " . $this->getPlugin()->getTranslation("commands.errors.radius-not-numeric"));
					return true;
				}
				if($args[1] > $this->getPlugin()->getSettings()->get("Maximum-Radius")) {
					$sender->sendMessage(TF::RED . "[Warning] " . $this->getPlugin()->getTranslation("commands.errors.radius-too-big"));
					return true;
				}
				Brush::setSize($sender, $args[1]);
				$sender->sendMessage(TF::GREEN . $this->getPlugin()->getTranslation("brush.size") . TF::AQUA . $args[1]);
				$action = Change::ACTION_CHANGE_SIZE;
				break;
			
			case "sh":
			case "shape":
				if(!BaseShape::isShape($args[1])) {
					$sender->sendMessage(TF::RED . "[Warning] " . $this->getPlugin()->getTranslation("commands.errors.shape-not-found"));
					return true;
				}
				if(!$sender->hasPermission("blocksniper.shape." . $args[1])) {
					$sender->sendMessage(TF::RED . "[Warning] " . $this->getPlugin()->getTranslation("commands.errors.no-permission"));
					return true;
				}
				Brush::setShape($sender, $args[1]);
				$sender->sendMessage(TF::GREEN . $this->getPlugin()->getTranslation("brush.shape") . TF::AQUA . Brush::getShape($sender)->getName());
				$action = Change::ACTION_CHANGE_SHAPE;
				break;
			
			case "ty":
			case "type":
				if(!BaseType::isType($args[1])) {
					$sender->sendMessage(TF::RED . "[Warning] " . $this->getPlugin()->getTranslation("commands.errors.shape-not-found"));
					return true;
				}
				if(!$sender->hasPermission("blocksniper.type." . $args[1])) {
					$sender->sendMessage(TF::RED . "[Warning] " . $this->getPlugin()->getTranslation("commands.errors.no-permission"));
					return true;
				}
				Brush::setType($sender, $args[1]);
				$sender->sendMessage(TF::GREEN . $this->getPlugin()->getTranslation("brush.type") . TF::AQUA . Brush::getType($sender)->getName());
				$action = Change::ACTION_CHANGE_TYPE;
				break;
			
			case "he":
			case "height":
				if(!is_numeric($args[1])) {
					$sender->sendMessage(TF::RED . "[Warning] " . $this->getPlugin()->getTranslation("commands.errors.radius-not-numeric"));
					return true;
				}
				Brush::setHeight($sender, $args[1]);
				$sender->sendMessage(TF::GREEN . $this->getPlugin()->getTranslation("brush.height") . TF::AQUA . $args[1]);
				$action = Change::ACTION_CHANGE_HEIGHT;
				break;
			
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
				$sender->sendMessage(TF::GREEN . $this->getPlugin()->getTranslation("brush.blocks") . TF::AQUA . implode(", ", $blockNames));
				$action = Change::ACTION_CHANGE_BLOCKS;
				break;
			
			case "ob":
			case "obsolete":
			case "replaced":
				$blocks = explode(",", $args[1]);
				Brush::setObsolete($sender, $blocks);
				$blocks = Brush::getObsolete($sender);
				$blockNames = [];
				foreach($blocks as $block) {
					$blockNames[] = $block->getName();
				}
				$sender->sendMessage(TF::GREEN . $this->getPlugin()->getTranslation("brush.obsolete") . TF::AQUA . implode(", ", $blockNames));
				$action = Change::ACTION_CHANGE_OBSOLETE;
				break;
			
			case "pe":
			case "perfect":
				Brush::setPerfect($sender, $args[1]);
				$sender->sendMessage(TF::GREEN . $this->getPlugin()->getTranslation("brush.perfect") . TF::AQUA . $args[1]);
				return true;
			
			case "gr": // TODO: Fix gravity and move return true to end.
			case "gravity":
				return true;
				Brush::setGravity($sender, $args[1]);
				$sender->sendMessage(TF::GREEN . $this->getPlugin()->getTranslation("brush.gravity") . TF::AQUA . $args[1]);
			
			case "decrement":
			case "decrementing":
			case "de":
				Brush::setDecrementing($sender, $args[1]);
				Brush::$resetSize[$sender->getId()] = Brush::getSize($sender);
				$sender->sendMessage(TF::GREEN . $this->getPlugin()->getTranslation("brush.decrement") . TF::AQUA . $args[1]);
				$action = Change::ACTION_CHANGE_DECREMENT;
				break;
			
			case "bi":
			case "biome":
				$biome = array_slice($args, 1);
				Brush::setBiome($sender, implode(" ", $biome));
				$sender->sendMessage(TF::GREEN . $this->getPlugin()->getTranslation("brush.biome") . TF::AQUA . Biome::getBiome(Brush::getBiomeId($sender))->getName());
				$action = Change::ACTION_CHANGE_BIOME;
				break;
				
			case "re":
			case "reset":
				Brush::resetBrush($sender);
				$sender->sendMessage(TF::GREEN . $this->getPlugin()->getTranslation("commands.succeed.brush.reset"));
				$action = Change::ACTION_RESET_BRUSH;
				break;
			
			case "ho":
			case "hollow":
				Brush::setHollow($sender, $args[1]);
				$sender->sendMessage(TF::GREEN . $this->getPlugin()->getTranslation("brush.hollow") . TF::AQUA . $args[1]);
				$action = Change::ACTION_CHANGE_HOLLOW;
				break;
			
			default:
				$sender->sendMessage(TF::RED . "[Usage] /brush <parameter> <value>");
				return true;
		}
		$this->getPlugin()->getServer()->getPluginManager()->callEvent(new Change($this->getPlugin(), $sender, $action, $args[0]));
		return true;
	}
}
