<?php

namespace Sandertv\BlockSniper\listeners;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerItemHeldEvent;
use pocketmine\event\player\PlayerQuitEvent;
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

class EventListener implements Listener {
	
	public function __construct(Loader $owner) {
		$this->owner = $owner;
	}
	
	public function getOwner(): Loader {
		return $this->owner;
	}
	
	public function onBrush(PlayerInteractEvent $event) {
		$player = $event->getPlayer();
		if(!$this->getOwner()->hasBrushWandEnabled($player)) {
			return;
		}
		$brushwand = $this->getOwner()->getBrushWand($player);
		$center = $player->getTargetBlock(100);
		if(!$center) {
			$player->sendMessage(TF::RED . "[Warning] " . $this->getPlugin()->getTranslation("commands.errors.no-target-found"));
			return;
		}
		
		switch($brushwand["type"]) {
			case "TYPE_CUBE":
				$shape = new CubeShape($this->getOwner(), $player->getLevel(), $brushwand["radius"], $center, explode(",", $brushwand["blocks"]));
				break;
			
			case "TYPE_SPHERE":
			case "TYPE_BALL":
				$shape = new SphereShape($this->getOwner(), $player->getLevel(), $brushwand["radius"], $center, explode(",", $brushwand["blocks"]));
				break;
			
			case "TYPE_OVERLAY":
				$shape = new OverlayType($this->getOwner(), $player->getLevel(), $brushwand["radius"], $center, explode(",", $brushwand["blocks"]));
				break;
			
			case "TYPE_DRAIN":
				$shape = new DrainType($this->getOwner(), $player->getLevel(), $brushwand["radius"], $center);
				break;
			
			case "TYPE_CLEAN":
			case "TYPE_CLEAR":
				$shape = new CleanType($this->getOwner(), $player->getLevel(), $brushwand["radius"], $center);
				break;
			
			case "TYPE_LEAFBLOWER":
				$shape = new LeafBlowerType($this->getOwner(), $player->getLevel(), $brushwand["radius"], $center);
				break;
			
			case "TYPE_REPLACE":
				if(!isset($brushwand["additionalData"])) {
					$player->sendMessage(TF::RED . "[Usage] /snipe replace <radius> <block to replace> <replacement>");
					return true;
				}
				$shape = new ReplaceType($this->getOwner(), $player->getLevel(), $brushwand["radius"], $center, $brushwand["blocks"], explode(",", $brushwand["additionalData"]));
				break;
			
			case "TYPE_CYLINDER":
			case "TYPE_STANDING_CYLINDER":
				if(strpos(strtolower($brushwand["radius"]), "x") === false) {
					$player->sendMessage(TF::RED . "[Usage] /brushwand cylinder <radiusXheight> <block(s)>");
					return true;
				}
				$sizes = explode("x", $brushwand["radius"]);
				$radius = $sizes[0];
				$height = $sizes[1];
				$shape = new CylinderStandingShape($this->getOwner(), $player->getLevel(), $radius, $height, $center, explode(",", $brushwand["blocks"]));
				break;
				
			case "TYPE_CUBOID":
				if(strpos(strtolower($brushwand["radius"]), "x") === false) {
					$player->sendMessage(TF::RED . "[Usage] /brushwand cuboid <widthXlengthXheight> <block(s)>");
					return true;
				}
				$sizes = explode("x", $brushwand["radius"]);
				$width = $sizes[0];
				$length = $sizes[1];
				$height = $sizes[2];
				$shape = new CuboidShape($this->getOwner(), $player->getLevel(), $width, $length, $height, $center, explode(",", $brushwand["blocks"]));
				break;
			
			case "TYPE_FLAT_LAYER":
			case "TYPE_LAYER":
				$shape = new LayerType($this->getOwner(), $player->getLevel(), $brushwand["radius"], $center, explode(",", $brushwand["blocks"]));
				break;
			
			case "TYPE_FLATTEN":
			case "TYPE_EQUALIZE":
				$shape = new FlattenType($this->getOwner(), $player->getLevel(), $brushwand["radius"], $center, explode(",", $brushwand["blocks"]));
				break;
		}
		
		if(!$player->hasPermission($shape->getPermission())) {
			$player->sendMessage(TF::RED . "[Warning] " . $this->getPlugin()->getTranslation("commands.errors.no-permission"));
			return true;
		}
		
		if($shape instanceof BaseType) {
			$this->getOwner()->getServer()->getPluginManager()->callEvent(new TypeCreateEvent($shape));
		} elseif($shape instanceof BaseShape) {
			$this->getOwner()->getServer()->getPluginManager()->callEvent(new ShapeCreateEvent($shape));
		}
		
		if(!$shape->fillShape()) {
			$player->sendMessage(TF::RED . "[Warning] " . $this->getPlugin()->getTranslation("commands.errors.no-valid-block"));
			return true;
		}
		
		$player->sendMessage(TF::GREEN . $this->getPlugin()->getTranslation("commands.succeed.default"));
		return true;
	}
	
	public function onItemSwitch(PlayerItemHeldEvent $event) {
		if($this->getOwner()->hasBrushWandEnabled($event->getPlayer())) {
			$this->getOwner()->disableBrushWand($event->getPlayer());
		}
	}
	
	public function onPlayerQuit(PlayerQuitEvent $event) {
		if($this->getOwner()->hasBrushWandEnabled($event->getPlayer())) {
			$this->getOwner()->disableBrushWand($event->getPlayer());
		}
	}
}
