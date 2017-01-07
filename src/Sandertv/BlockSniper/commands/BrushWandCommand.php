<?php

namespace Sandertv\BlockSniper\commands;

use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat as TF;
use Sandertv\BlockSniper\Loader;
use Sandertv\BlockSniper\commands\BaseCommand;
use pocketmine\Player;
use Sandertv\BlockSniper\brush\shapes\CuboidShape;
use Sandertv\BlockSniper\brush\shapes\SphereShape;
use Sandertv\BlockSniper\brush\shapes\CylinderStandingShape;
use Sandertv\BlockSniper\brush\types\OverlayType;
use Sandertv\BlockSniper\brush\types\LayerType;
use Sandertv\BlockSniper\brush\types\ReplaceType;
use Sandertv\BlockSniper\brush\types\FlattenType;
use Sandertv\BlockSniper\brush\types\DrainType;
use Sandertv\BlockSniper\brush\types\CleanType;
use Sandertv\BlockSniper\brush\types\LeafBlowerType;

class BrushWandCommand extends BaseCommand {
    
    public function __construct(Loader $owner) {
        parent::__construct($owner, "brushwand", "Switch off/on the Brush Wand", "<type|off> <radius> <block(s)>", ["bw"]);
        $this->setPermission("blocksniper.command.brushwand");
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
        
        if((count($args) < 2 || count($args) > 4)) {
            $sender->sendMessage(TF::RED . "[Usage] /brushwand <type> <radius> <block(s)>");
            return true;
        }
        
        $type = ("TYPE_" . strtoupper($args[0]));
        if(strtolower($args[0]) === "off") {
            $sender->sendMessage(TF::GREEN . "Brush wand disabled.");
            $this->getPlugin()->disableBrushWand($sender);
            return true;
        }
        
        if(!is_numeric($args[1])  && $type !== "TYPE_CYLINDER" && $type !== "TYPE_STANDING_CYLINDER") {
            $sender->sendMessage(TF::RED . "[Warning] The radius should be numeric.");
            return true;
        }
        
        if($args[1] > $this->getSettings()->get("Maximum-Radius")) {
            $sender->sendMessage(TF::RED . "[Warning] That radius is too big. Please set a radius of " . $this->getSettings()->get("Maximum-Radius") . " or smaller.");
            return true;
        }
        
        switch($type) {
            case "TYPE_CUBE":
            case "TYPE_CUBOID":
                $shape = new CuboidShape($sender->getLevel());
                break;
            
            case "TYPE_SPHERE":
            case "TYPE_BALL":
                $shape = new SphereShape($sender->getLevel());
                break;
                
            case "TYPE_CYLINDER":
            case "TYPE_STANDING_CYLINDER":
                $shape = new CylinderStandingShape($sender->getLevel());
                break;
                
            case "TYPE_REPLACE":
                $shape = new ReplaceType($sender->getLevel());
                break;
            
            case "TYPE_DRAIN":
                $shape = new DrainType($sender->getLevel());
                break;
            
            case "TYPE_OVERLAY":
                $shape = new OverlayType($sender->getLevel());
                break;
            
            case "TYPE_FLATTEN":
            case "TYPE_EQUALIZE":
                $shape = new FlattenType($sender->getLevel());
                break;
                
            case "TYPE_FLAT_LAYER":
            case "TYPE_LAYER":
                $shape = new LayerType($sender->getLevel());
                break;
            
            case "TYPE_CLEAN":
            case "TYPE_CLEAR":
                $shape = new CleanType($sender->getLevel());
                break;
            
            case "TYPE_LEAFBLOWER":
                $shape = new LeafBlowerType($sender->getLevel());
                break;
            
            default:
                $sender->sendMessage(TF::RED . "[Warning] Shape not found.");
                return true;
        }
        
        if(!$sender->hasPermission($shape->getPermission())) {
            $sender->sendMessage(TF::RED . "[Warning] You do not have permission to use this type.");
            return true;
        }
        $sender->sendMessage(TF::GREEN . "Brush wand has been enabled.");
        $this->getPlugin()->enableBrushWand($sender, $type, $args[1], isset($args[2]) ? $args[2] : null, isset($args[3]) ? $args[3] : null);
        return true;
    }
}
