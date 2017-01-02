<?php

namespace Sandertv\BlockSniper\listeners;

use pocketmine\event\Listener;
use pocketmine\utils\TextFormat as TF;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerItemHeldEvent;
use pocketmine\event\player\PlayerQuitEvent;
use Sandertv\BlockSniper\Loader;
use Sandertv\BlockSniper\shapes\CuboidShape;
use Sandertv\BlockSniper\shapes\SphereShape;
use Sandertv\BlockSniper\shapes\OverlayShape;

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
        
        switch($brushwand["type"]) {
            case "TYPE_CUBE":
            case "TYPE_CUBOID":
                $cube = new CuboidShape($player->getLevel(), $brushwand["radius"], $center, explode(",", $brushwand["blocks"]));
                if(!$player->hasPermission($cube->getPermission())) {
                    $player->sendMessage(TF::RED . "[Warning] You do not have permission to use the Cube shape.");
                    break;
                }
                if(!$cube->fillShape()) {
                    $player->sendMessage(TF::RED . "[Warning] Invalid block given.");
                    break;
                }
                $player->sendPopup(TF::GREEN . "Succesfully launched a cube at the location looked at.");
                break;
            
            case "TYPE_SPHERE":
            case "TYPE_BALL":
                $sphere = new SphereShape($player->getLevel(), $brushwand["radius"], $center, explode(",", $brushwand["blocks"]));
                if(!$player->hasPermission($sphere->getPermission())) {
                    $player->sendMessage(TF::RED . "[Warning] You do not have permission to use the Sphere shape.");
                    break;
                }
                if(!$sphere->fillShape()) {
                    $player->sendMessage(TF::RED . "[Warning] Invalid block given.");
                    break;
                }
                $player->sendPopup(TF::GREEN . "Succesfully launched a sphere at the location looked at.");
                break;
                
            case "TYPE_OVERLAY":
                $overlay = new OverlayShape($player->getLevel(), $brushwand["radius"], $center, explode(",", $brushwand["blocks"]));
                if(!$player->hasPermission($overlay->getPermission())) {
                    $player->sendMessage(TF::RED . "[Warning] You do not have permission to use the Overlay shape.");
                    break;
                }
                if(!$overlay->fillShape()) {
                    $player->sendMessage(TF::RED . "[Warning] Invalid block given.");
                    break;
                }
                $player->sendPopup(TF::GREEN . "Succesfully launched an overlay at the location looked at.");
                break;
        }
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
