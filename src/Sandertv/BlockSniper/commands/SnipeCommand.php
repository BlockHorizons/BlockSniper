<?php

namespace Sandertv\BlockSniper\commands;

use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat as TF;
use Sandertv\BlockSniper\Loader;
use Sandertv\BlockSniper\commands\BaseCommand;
use pocketmine\Player;
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

class SnipeCommand extends BaseCommand {
    
    public function __construct(Loader $owner) {
        parent::__construct($owner, "snipe", "Snipe a small cluster of blocks at the location you're looking", "<type> <radius> <block(s)>", ["shoot", "launch"]);
        $this->setPermission("blocksniper.command.snipe");
    }
    
    /**
     * @param CommandSender $sender
     * @param type $commandLabel
     * @param array $args
     * @return boolean
     */
    public function execute(CommandSender $sender, $commandLabel, array $args) {
        if(!$this->testPermission($sender)) {
            $this->sendNoPermission($sender);
        }
        
        if(!$sender instanceof Player) {
            $this->sendConsoleError($sender);
            return true;
        }
        
        if(count($args) < 3 || count($args) > 4) {
            $sender->sendMessage(TF::RED . "[Usage] /snipe <type> <radius> <block(s)>");
            return true;
        }
        
        $type = ("TYPE_" . strtoupper($args[0]));
        
        if(!is_numeric($args[1]) && $type !== "TYPE_CYLINDER" && $type !== "TYPE_STANDING_CYLINDER" && $type !== "TYPE_CYLINDER_STANDING") {
            $sender->sendMessage(TF::RED . "[Warning] The radius should be numeric.");
            return true;
        }
        
        if($args[1] > 10) { // TODO: Make this configurable.
            $sender->sendMessage(TF::RED . "[Warning] That radius is too big. Please set a radius of 10 or smaller.");
            return true;
        }
        
        $center = $sender->getTargetBlock(100);
        if(!$center) {
            $sender->sendMessage(TF::RED . "[Warning] No target block could be found.");
            return true;
        }
        
        switch($type) {   
            case "TYPE_CUBE":
            case "TYPE_CUBOID":
                $shape = new CuboidShape($sender->getLevel(), $args[1], $center, explode(",", $args[2]));
                break;
                
            case "TYPE_SPHERE":
            case "TYPE_BALL":
                $shape = new SphereShape($sender->getLevel(), $args[1], $center, explode(",", $args[2]));
                break;
            
            case "TYPE_REPLACE":
                if(!isset($args[3])) {
                    $sender->sendMessage(TF::RED . "[Usage] /snipe replace <radius> <block to replace> <replacement>");
                    return true;
                }
                $shape = new ReplaceType($sender->getLevel(), $args[1], $center, $args[2], explode(",", $args[3]));
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
                $shape = new CylinderStandingShape($sender->getLevel(), $radius, $height, $center, explode(",", $args[2]));
                break;
                
            case "TYPE_OVERLAY":
                $shape = new OverlayType($sender->getLevel(), $args[1], $center, explode(",", $args[2]));
                break;
                
            case "TYPE_FLAT_LAYER":
            case "TYPE_LAYER":
                $shape = new LayerType($sender->getLevel(), $args[1], $center, explode(",", $args[2]));
                break;
            
            default:
                $sender->sendMessage(TF::RED . "[Warning] Type not found.");
                return true;
        }
        
        if(!$sender->hasPermission($shape->getPermission())) {
            $sender->sendMessage(TF::RED . "[Warning] You don't have permission to use this type.");
            return true;
        }
        
        if($shape instanceof BaseType) {
            $this->getPlugin()->getServer()->getPluginManager()->callEvent(new TypeCreateEvent($shape));
        } elseif($shape instanceof BaseShape) {
            $this->getPlugin()->getServer()->getPluginManager()->callEvent(new ShapeCreateEvent($shape));
        }
        
        if(!$shape->fillShape()) {
            $sender->sendMessage(TF::RED . "[Warning] Invalid block given.");
            return true;
        }
        
        $sender->sendMessage(TF::GREEN . "Succesfully launched a(n) " . $args[0] . " at the location looked at.");
        return true;
    }
}
