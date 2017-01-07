<?php

namespace Sandertv\BlockSniper\commands;

use pocketmine\command\Command;
use pocketmine\command\PluginIdentifiableCommand;
use Sandertv\BlockSniper\Loader;
use pocketmine\utils\TextFormat as TF;
use pocketmine\command\CommandSender;

abstract class BaseCommand extends Command implements PluginIdentifiableCommand {
    
    public function __construct(Loader $owner, $name, $description = "", $usageMessage = null, array $aliases = []){
        parent::__construct($name, $description, $usageMessage, $aliases);
        $this->owner = $owner;
        $this->usageMessage = "";
    }

    /**
     * @return Loader
     */
    public function getPlugin(): Loader {
        return $this->owner;
    }
    
    /**
     * @param CommandSender $sender
     */
    public function sendConsoleError(CommandSender $sender) {
        $sender->sendMessage(TF::RED . "[Warning] You can't execute this command using console.");
    }
    
    public function sendNoPermission(CommandSender $sender) {
        $sender->sendMessage(TF::RED . "[Warning] You don't have permission to execute this command.");
    }
    
    public function getSettings() {
        return $this->getPlugin()->settings;
    }
}
