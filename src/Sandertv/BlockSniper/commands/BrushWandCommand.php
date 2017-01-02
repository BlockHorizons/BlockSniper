<?php

namespace Sandertv\BlockSniper\commands;

use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat as TF;
use Sandertv\BlockSniper\Loader;
use Sandertv\BlockSniper\commands\BaseCommand;
use pocketmine\Player;
use Sandertv\BlockSniper\shapes\CuboidShape;
use Sandertv\BlockSniper\shapes\SphereShape;

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
        
        if(count($args) < 3 || count($args) > 3) {
            $sender->sendMessage(TF::RED . "[Usage] /brushwand <type> <radius> <block(s)>");
            return true;
        }
        
        $type = ("TYPE_" . strtoupper($args[0]));
        if(strtolower($args[0]) === "off") {
            $sender->sendMessage(TF::GREEN . "Brush wand disabled.");
            $this->getPlugin()->disableBrushWand($sender);
            return true;
        }
        
        if(!is_numeric($args[1])) {
            $sender->sendMessage(TF::RED . "[Warning] The radius should be numeric.");
            return true;
        }
        
        if($args[1] > 10) { // TODO: Make this configurable
            $sender->sendMessage(TF::RED . "[Warning] That radius is too big. Please set a radius of 10 or smaller.");
            return true;
        }
        
        switch($type) {
            case "TYPE_CUBE":
            case "TYPE_CUBOID":
                $cube = new CuboidShape($sender->getLevel(), $args[1], $center, explode(",", $args[2]));
                if(!$sender->hasPermission($cube->getPermission())) {
                    $sender->sendMessage(TF::RED . "[Warning] You do not have permission to use the Cube shape.");
                    return true;
                }
                
                $sender->sendMessage(TF::GREEN . "Brush wand has been enabled.");
                $this->getPlugin()->enableBrushWand($sender, $type, $args[1], explode(",", $args[2]));
                break;
            
            case "TYPE_SPHERE":
            case "TYPE_BALL":
                $sphere = new SphereShape($sender->getLevel(), $args[1], $center, explode(",", $args[2]));
                if(!$sender->hasPermission($sphere->getPermission())) {
                    $sender->sendMessage(TF::RED . "[Warning] You do not have permission to use the Sphere shape.");
                    return true;
                }
                
                $sender->sendMessage(TF::GREEN . "Brush wand has been enabled.");
                break;
                
            default:
                $sender->sendMessage(TF::RED . "[Warning] Please provide a valid shape.");
                return true;
        }
    }
}
