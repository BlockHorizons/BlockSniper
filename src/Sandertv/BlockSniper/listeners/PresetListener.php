<?php

namespace Sandertv\BlockSniper\listeners;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;
use Sandertv\BlockSniper\Loader;

class PresetListener implements Listener {
	
	public $main;
	
	public function __construct(Loader $main) {
		$this->main = $main;
	}
	
	public function onChat(PlayerChatEvent $event) {
		$player = $event->getPlayer();
		if(!$this->getOwner()->getPresetManager()->isCreatingAPreset($player)) {
			return;
		}
		$message = explode(" ", $event->getMessage());
		
		switch($message[0]) {
			case "cancel":
			
		}
	}
	
	public function getOwner(): Loader {
		return $this->main;
	}
}