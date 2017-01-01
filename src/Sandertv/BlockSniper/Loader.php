<?php

namespace Sandertv\BlockSniper;

use pocketmine\plugin\PluginBase;
use pocketmine\utils\TextFormat as TF;

class Loader extends PluginBase {
    
    
    public function onEnable() {
        
        $this->getLogger()->info(TF::GREEN . "BlockSniper has been enabled");
        
        if(!is_dir($this->getDataFolder())) {
            mkdir($this->getDataFolder());
        }
        $this->saveResource("settings.yml");
        $this->settings = new Config($this->getDataFolder() . "settings.yml", Config::YAML);
        
    }
    
    
    public function onDisable() {
        
        $this->getLogger()->info(TF::RED . "BlockSniper has been disabled");
        
    }
    
}
