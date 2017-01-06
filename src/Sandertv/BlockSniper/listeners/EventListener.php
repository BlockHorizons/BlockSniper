<?php

namespace Sandertv\BlockSniper\listeners;

use pocketmine\event\Listener;
use pocketmine\utils\TextFormat as TF;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerItemHeldEvent;
use pocketmine\event\player\PlayerQuitEvent;
use Sandertv\BlockSniper\Loader;
use Sandertv\BlockSniper\events\TypeCreateEvent;
use Sandertv\BlockSniper\events\ShapeCreateEvent;
use Sandertv\BlockSniper\brush\BaseShape;
use Sandertv\BlockSniper\brush\BaseType;
use Sandertv\BlockSniper\brush\shapes\CuboidShape;
use Sandertv\BlockSniper\brush\shapes\SphereShape;
use Sandertv\BlockSniper\brush\shapes\CylinderStandingShape;
use Sandertv\BlockSniper\brush\types\OverlayType;
use Sandertv\BlockSniper\brush\types\LayerType;
use Sandertv\BlockSniper\brush\types\ReplaceType;
use Sandertv\BlockSniper\brush\types\FlattenType;

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
            $player->sendMessage(TF::RED . "[Warning] Could not find a valid target block.");
            return;
        }
        
        switch($brushwand["type"]) {
            case "TYPE_CUBE":
            case "TYPE_CUBOID":
                $shape = new CuboidShape($player->getLevel(), $brushwand["radius"], $center, explode(",", $brushwand["blocks"]));
                break;
            
            case "TYPE_SPHERE":
            case "TYPE_BALL":
                $shape = new SphereShape($player->getLevel(), $brushwand["radius"], $center, explode(",", $brushwand["blocks"]));
                break;
                
            case "TYPE_OVERLAY":
                $shape = new OverlayType($player->getLevel(), $brushwand["radius"], $center, explode(",", $brushwand["blocks"]));
                break;
            
            case "TYPE_DRAIN":
                $shape = new DrainType($player->getLevel(), $brushwand["radius"], $center);
                break;
                
            case "TYPE_REPLACE":
                if(!isset($brushwand["additionalData"])) {
                    $sender->sendMessage(TF::RED . "[Usage] /snipe replace <radius> <block to replace> <replacement>");
                    return true;
                }
                $shape = new ReplaceType($player->getLevel(), $brushwand["radius"], $center, $brushwand["blocks"], explode(",", $brushwand["additionalData"]));
                break;
                
            case "TYPE_CYLINDER":
            case "TYPE_STANDING_CYLINDER":
                if(strpos(strtolower($args[1]), "x") === false) {
                    $player->sendMessage(TF::RED . "[Usage] /snipe cylinder <radiusXheight> <block(s)>");
                    return true;
                }
                $sizes = explode("x", $brushwand["radius"]);
                $radius = $sizes[0];
                $height = $sizes[1];
                $shape = new CylinderStandingShape($player->getLevel(), $radius, $height, $center, explode(",", $brushwand["blocks"]));
                break;
                
                
            case "TYPE_FLAT_LAYER":
            case "TYPE_LAYER":
                $shape = new LayerType($player->getLevel(), $brushwand["radius"], $center, explode(",", $brushwand["blocks"]));
                break;
            
            case "TYPE_FLATTEN":
            case "TYPE_EQUALIZE":
                $shape = new FlattenType($player->getLevel(), $brushwand["radius"], $center, explode(",", $brushwand["blocks"]));
                break;
        }
        
        if(!$player->hasPermission($shape->getPermission())) {
            $player->sendMessage(TF::RED . "[Warning] You do not have permission to use this shape.");
            return true;
        }
        
        if($shape instanceof BaseType) {
            $this->getOwner()->getServer()->getPluginManager()->callEvent(new TypeCreateEvent($shape));
        } elseif($shape instanceof BaseShape) {
            $this->getOwner()->getServer()->getPluginManager()->callEvent(new ShapeCreateEvent($shape));
        }
        
        if(!$shape->fillShape()) {
            $player->sendMessage(TF::RED . "[Warning] Invalid block given.");
            return true;
        }
        
        $player->sendPopup(TF::GREEN . "Succesfully launched the shape at the location looked at.");
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
