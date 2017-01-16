<?php

namespace Sandertv\BlockSniper\commands;

use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\utils\TextFormat as TF;
use Sandertv\BlockSniper\brush\BaseShape;
use Sandertv\BlockSniper\brush\BaseType;
use Sandertv\BlockSniper\brush\shapes\CubeShape;
use Sandertv\BlockSniper\brush\shapes\CylinderStandingShape;
use Sandertv\BlockSniper\brush\shapes\SphereShape;
use Sandertv\BlockSniper\brush\shapes\CuboidShape;
use Sandertv\BlockSniper\brush\types\CleanType;
use Sandertv\BlockSniper\brush\types\DrainType;
use Sandertv\BlockSniper\brush\types\FlattenType;
use Sandertv\BlockSniper\brush\types\LayerType;
use Sandertv\BlockSniper\brush\types\LeafBlowerType;
use Sandertv\BlockSniper\brush\types\OverlayType;
use Sandertv\BlockSniper\brush\types\ReplaceType;
use Sandertv\BlockSniper\events\ShapeCreateEvent;
use Sandertv\BlockSniper\events\TypeCreateEvent;
use Sandertv\BlockSniper\Loader;

class SnipeCommand extends BaseCommand {
	
	public function __construct(Loader $owner) {
		parent::__construct($owner, "snipe", "Snipe a small cluster of blocks at the location you're looking", "<type> <radius> <block(s)>", ["shoot", "launch"]);
		$this->setPermission("blocksniper.command.snipe");
	}
	
	/**
	 * @param CommandSender $sender
	 * @param type          $commandLabel
	 * @param array         $args
	 *
	 * @return boolean
	 */
	public function execute(CommandSender $sender, $commandLabel, array $args) {
		if(!$this->testPermission($sender)) {
			$this->sendNoPermission($sender);
			return true;
		}
		
		if(!$sender instanceof Player) {
			$this->sendConsoleError($sender);
			return true;
		}
		
		if((count($args) < 2 || count($args) > 4)) {
			$sender->sendMessage(TF::RED . "[Usage] /snipe <type> <radius> <block(s)>");
			return true;
		}
		
		$type = ("TYPE_" . strtoupper($args[0]));
		
		if(!is_numeric($args[1]) && $type !== "TYPE_CYLINDER" && $type !== "TYPE_STANDING_CYLINDER" && $type !== "TYPE_CUBOID") {
			$sender->sendMessage(TF::RED . "[Warning] " . $this->getPlugin()->getTranslation("commands.errors.radius-not-numeric"));
			return true;
		}
		
		if($args[1] > $this->getSettings()->get("Maximum-Radius")) {
			$sender->sendMessage(TF::RED . "[Warning] " . $this->getPlugin()->getTranslation("commands.errors.radius-too-big"));
			return true;
		}
		
		$center = $sender->getTargetBlock(100);
		if(!$center) {
			$sender->sendMessage(TF::RED . "[Warning] " . $this->getPlugin()->getTranslation("commands.errors.no-target-found"));
			return true;
		}
		
		switch($type) {
			case "TYPE_CUBE":
				$shape = new CubeShape($this->getPlugin(), $sender->getLevel(), $args[1], $center, explode(",", $args[2]));
				break;
			
			case "TYPE_SPHERE":
			case "TYPE_BALL":
				$shape = new SphereShape($this->getPlugin(), $sender->getLevel(), $args[1], $center, explode(",", $args[2]));
				break;
			
			case "TYPE_REPLACE":
				if(!isset($args[3])) {
					$sender->sendMessage(TF::RED . "[Usage] /snipe replace <radius> <block to replace> <replacement>");
					return true;
				}
				$shape = new ReplaceType($this->getPlugin(), $sender->getLevel(), $args[1], $center, $args[2], explode(",", $args[3]));
				break;
			
			case "TYPE_FLATTEN":
			case "TYPE_EQUALIZE":
				$shape = new FlattenType($this->getPlugin(), $sender->getLevel(), $args[1], $center, explode(",", $args[2]));
				break;
			
			case "TYPE_DRAIN":
				$shape = new DrainType($this->getPlugin(), $sender->getLevel(), $args[1], $center);
				break;
			
			case "TYPE_CLEAR":
			case "TYPE_CLEAN":
				$shape = new CleanType($this->getPlugin(), $sender->getLevel(), $args[1], $center);
				break;
			
			case "TYPE_LEAFBLOWER":
				$shape = new LeafBlowerType($this->getPlugin(), $sender->getLevel(), $args[1], $center);
				break;
			
			case "TYPE_CYLINDER":
			case "TYPE_CYLINDER_STANDING":
			case "TYPE_STANDING_CYLINDER":
				if(strpos(strtolower($args[1]), "x") === false) {
					$sender->sendMessage(TF::RED . "[Usage] /snipe cylinder <radiusXheight> <block(s)>");
					return true;
				}
				$sizes = explode("x", $args[1]);
				$radius = $sizes[0];
				$height = $sizes[1];
				$shape = new CylinderStandingShape($this->getPlugin(), $sender->getLevel(), $radius, $height, $center, explode(",", $args[2]));
				break;
			
			case "TYPE_CUBOID":
				if(strpos(strtolower($args[1]), "x") === false) {
					$sender->sendMessage(TF::RED . "[Usage] /brushwand cuboid <widthXlengthXheight> <block(s)>");
					return true;
				}
				$sizes = explode("x", $args[1]);
				$width = $sizes[0];
				$length = $sizes[1];
				$height = $sizes[2];
				$shape = new CuboidShape($this->getPlugin(), $sender->getLevel(), $width, $length, $height, $center, explode(",", $args[2]));
				break;
				
			case "TYPE_OVERLAY":
				$shape = new OverlayType($this->getPlugin(), $sender->getLevel(), $args[1], $center, explode(",", $args[2]));
				break;
			
			case "TYPE_FLAT_LAYER":
			case "TYPE_LAYER":
				$shape = new LayerType($this->getPlugin(), $sender->getLevel(), $args[1], $center, explode(",", $args[2]));
				break;
			
			default:
				$sender->sendMessage(TF::RED . "[Warning] " . $this->getPlugin()->getTranslation("commands.errors.shape-not-found"));
				return true;
		}
		
		if(!$sender->hasPermission($shape->getPermission())) {
			$sender->sendMessage(TF::RED . "[Warning] " . $this->getPlugin()->getTranslation("commands.errors.no-permission"));
			return true;
		}
		
		if($shape instanceof BaseType) {
			$this->getPlugin()->getServer()->getPluginManager()->callEvent(new TypeCreateEvent($shape));
		} elseif($shape instanceof BaseShape) {
			$this->getPlugin()->getServer()->getPluginManager()->callEvent(new ShapeCreateEvent($shape));
		}
		
		if(!$shape->fillShape()) {
			$sender->sendMessage(TF::RED . "[Warning] " . $this->getPlugin()->getTranslation("commands.errors.no-valid-block"));
			return true;
		}
		
		$sender->sendMessage(TF::GREEN . $this->getPlugin()->getTranslation("commands.succeed.default"));
		return true;
	}
}
