<?php

namespace Sandertv\BlockSniper\commands;

use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat as TF;
use Sandertv\BlockSniper\Loader;
use Sandertv\BlockSniper\commands\BaseCommand;
use pocketmine\Player;
use Sandertv\BlockSniper\shapes\BaseShape;
use Sandertv\BlockSniper\shapes\SphereShape;

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
            return false;
        }
        
        if(!$sender instanceof Player) {
            $this->sendConsoleError($sender);
            return true;
        }
        
        if(count($args) < 3) {
            $sender->sendMessage(TF::RED . "[Usage] /snipe <type> <radius> <block(s)>");
            return true;
        }
        
        $type = ("TYPE_" . strtoupper($args[0]));
        
        if(!is_numeric($args[1])) {
            $sender->sendMessage(TF::RED . "[Warning] The radius should be numeric.");
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
                // TODO
                break;
            
            case "TYPE_SPHERE":
            case "TYPE_BALL":
                $sphere = new SphereShape($sender->getLevel(), $args[1], $center, array_slice($args, 2));
                if(!$sender->hasPermission($sphere->getPermission())) {
                    $sender->sendMessage(TF::RED . "[Warning] You do not have permission to use this shape.");
                }
                if($args[1] > 10) {
                    $sender->sendMessage(TF::RED . "[Warning] That radius is too big. Please set a radius of 10 or smaller.");
                    return true;
                }
                if(!$sphere->fillShape()) {
                    $sender->sendMessage(TF::RED . "[Warning] Invalid block given.");
                    return true;
                }
                $sender->sendMessage(TF::GREEN . "Succesfully launched a sphere at the location looked at.");
                break;
                
            default:
                $sender->sendMessage(TF::RED . "[Warning] Please provide a valid shape.");
                return true;
        }
        return false;
    }
}
