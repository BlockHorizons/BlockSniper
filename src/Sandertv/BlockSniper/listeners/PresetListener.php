<?php

namespace Sandertv\BlockSniper\listeners;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\utils\TextFormat as TF;
use Sandertv\BlockSniper\Loader;

class PresetListener implements Listener {
	
	public $main;
	
	public function __construct(Loader $main) {
		$this->main = $main;
	}
	
	public function onChat(PlayerChatEvent $event) {
		$player = $event->getPlayer();
		if(!$this->getOwner()->getPresetManager()->isCreatingAPreset($player)) {
			return false;
		}
		$message = explode(" ", $event->getMessage());
		
		if(strtolower($message[0]) === "cancel") {
			$this->getOwner()->getPresetManager()->cancelPresetCreationProcess($player);
			$player->sendMessage(TF::YELLOW . $this->getOwner()->getTranslation("commands.succeed.preset.canceled"));
			return true;
		}
		
		// Ew... I know.
		switch($this->getOwner()->getPresetManager()->getCurrentPresetCreationProgress($player)) {
			case 0:
				$player->sendMessage(TF::AQUA . strtolower($message[0]));
				$this->getOwner()->getPresetManager()->addToCreationData($player, "name", strtolower($message[0]));
				$player->sendMessage(TF::GRAY . $this->getOwner()->getTranslation("brush.shape"));
				break;
			case 1:
				$player->sendMessage(TF::GREEN . $this->getOwner()->getTranslation("brush.shape") . TF::AQUA . $message[0]);
				$this->getOwner()->getPresetManager()->addToCreationData($player, "shape", strtolower($message[0]));
				$player->sendMessage(TF::GRAY . $this->getOwner()->getTranslation("brush.type"));
				break;
			case 2:
				$player->sendMessage(TF::GREEN . $this->getOwner()->getTranslation("brush.type") . TF::AQUA . $message[0]);
				$this->getOwner()->getPresetManager()->addToCreationData($player, "type", strtolower($message[0]));
				$player->sendMessage(TF::GRAY . $this->getOwner()->getTranslation("brush.decrement"));
				break;
			case 3:
				$player->sendMessage(TF::GREEN . $this->getOwner()->getTranslation("brush.decrement") . TF::AQUA . $message[0]);
				$this->getOwner()->getPresetManager()->addToCreationData($player, "decrement", strtolower($message[0]));
				$player->sendMessage(TF::GRAY . $this->getOwner()->getTranslation("brush.size"));
				break;
			case 4:
				$player->sendMessage(TF::GREEN . $this->getOwner()->getTranslation("brush.size") . TF::AQUA . $message[0]);
				$this->getOwner()->getPresetManager()->addToCreationData($player, "size", strtolower($message[0]));
				$player->sendMessage(TF::GRAY . $this->getOwner()->getTranslation("brush.hollow"));
				break;
			case 5:
				$player->sendMessage(TF::GREEN . $this->getOwner()->getTranslation("brush.hollow") . TF::AQUA . $message[0]);
				$this->getOwner()->getPresetManager()->addToCreationData($player, "hollow", strtolower($message[0]));
				$player->sendMessage(TF::GRAY . $this->getOwner()->getTranslation("brush.height"));
				break;
			case 6:
				$player->sendMessage(TF::GREEN . $this->getOwner()->getTranslation("brush.height") . TF::AQUA . $message[0]);
				$this->getOwner()->getPresetManager()->addToCreationData($player, "height", strtolower($message[0]));
				$player->sendMessage(TF::GRAY . $this->getOwner()->getTranslation("brush.biome"));
				break;
			case 7:
				$player->sendMessage(TF::GREEN . $this->getOwner()->getTranslation("brush.biome") . TF::AQUA . $message[0]);
				$this->getOwner()->getPresetManager()->addToCreationData($player, "biome", strtolower($message[0]));
				$player->sendMessage(TF::GRAY . $this->getOwner()->getTranslation("brush.obsolete"));
				break;
			case 8:
				$player->sendMessage(TF::GREEN . $this->getOwner()->getTranslation("brush.obsolete") . TF::AQUA . $message[0]);
				$this->getOwner()->getPresetManager()->addToCreationData($player, "obsolete", strtolower($message[0]));
				$player->sendMessage(TF::GRAY . $this->getOwner()->getTranslation("brush.blocks"));
				break;
			case 9:
				$player->sendMessage(TF::GREEN . $this->getOwner()->getTranslation("brush.blocks") . TF::AQUA . $message[0]);
				$this->getOwner()->getPresetManager()->addToCreationData($player, "blocks", strtolower($message[0]));
				$this->getOwner()->getPresetManager()->parsePresetCreationInfo($player, $this->getOwner()->getPresetManager()->getCreationData($player, "name"));
				$player->sendMessage(TF::GREEN . "Preset creation process finished successfully.");
				break;
		}
	}
	
	public function getOwner(): Loader {
		return $this->main;
	}
}