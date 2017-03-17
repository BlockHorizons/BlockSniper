<?php

namespace Sandertv\BlockSniper\listeners;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\utils\TextFormat as TF;
use Sandertv\BlockSniper\brush\BaseShape;
use Sandertv\BlockSniper\brush\BaseType;
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
			$event->setCancelled();
			return true;
		}
		
		// Ew... I know.
		switch($this->getOwner()->getPresetManager()->getCurrentPresetCreationProgress($player)) {
			case 0:
				$player->sendMessage(TF::AQUA . $message[0]);
				$this->getOwner()->getPresetManager()->addToCreationData($player, "name", $message[0]);
				$player->sendMessage(TF::GRAY . $this->getOwner()->getTranslation("brush.shape"));
				break;
			case 1:
				if(!BaseShape::isShape(strtolower($message[0]))) {
					$player->sendMessage(TF::RED . "[Warning] " . $this->getOwner()->getTranslation("commands.errors.shape-not-found"));
					return false;
				}
				$player->sendMessage(TF::GREEN . $this->getOwner()->getTranslation("brush.shape") . TF::AQUA . $message[0]);
				$this->getOwner()->getPresetManager()->addToCreationData($player, "shape", strtolower($message[0]));
				$player->sendMessage(TF::GRAY . $this->getOwner()->getTranslation("brush.type"));
				break;
			case 2:
				if(!BaseType::isType(strtolower($message[0]))) {
					$player->sendMessage(TF::RED . "[Warning] " . $this->getOwner()->getTranslation("commands.errors.shape-not-found"));
					return false;
				}
				$player->sendMessage(TF::GREEN . $this->getOwner()->getTranslation("brush.type") . TF::AQUA . $message[0]);
				$this->getOwner()->getPresetManager()->addToCreationData($player, "type", strtolower($message[0]));
				$player->sendMessage(TF::GRAY . $this->getOwner()->getTranslation("brush.decrement"));
				break;
			case 3:
				$message[0] = (bool)$message[0];
				if(!is_bool($message[0])) {
					return false;
				}
				$player->sendMessage(TF::GREEN . $this->getOwner()->getTranslation("brush.decrement") . TF::AQUA . $message[0]);
				$this->getOwner()->getPresetManager()->addToCreationData($player, "decrement", $message[0]);
				$player->sendMessage(TF::GRAY . $this->getOwner()->getTranslation("brush.size"));
				break;
			case 4:
				if(!is_numeric($message[0])) {
					return false;
				}
				if($message[0] > $this->getOwner()->getSettings()->get("Maximum-Radius")) {
					$player->sendMessage(TF::RED . "[Warning] " . $this->getOwner()->getTranslation("commands.errors.radius-too-big"));
					return false;
				}
				$player->sendMessage(TF::GREEN . $this->getOwner()->getTranslation("brush.size") . TF::AQUA . $message[0]);
				$this->getOwner()->getPresetManager()->addToCreationData($player, "size", $message[0]);
				$player->sendMessage(TF::GRAY . $this->getOwner()->getTranslation("brush.hollow"));
				break;
			case 5:
				$message[0] = (bool)$message[0];
				if(!is_bool($message[0])) {
					return false;
				}
				$player->sendMessage(TF::GREEN . $this->getOwner()->getTranslation("brush.hollow") . TF::AQUA . $message[0]);
				$this->getOwner()->getPresetManager()->addToCreationData($player, "hollow", $message[0]);
				$player->sendMessage(TF::GRAY . $this->getOwner()->getTranslation("brush.height"));
				break;
			case 6:
				if(!is_numeric($message[0])) {
					return false;
				}
				if($message[0] > $this->getOwner()->getSettings()->get("Maximum-Radius")) {
					$player->sendMessage(TF::RED . "[Warning] " . $this->getOwner()->getTranslation("commands.errors.radius-too-big"));
					return false;
				}
				$player->sendMessage(TF::GREEN . $this->getOwner()->getTranslation("brush.height") . TF::AQUA . $message[0]);
				$this->getOwner()->getPresetManager()->addToCreationData($player, "height", $message[0]);
				$player->sendMessage(TF::GRAY . $this->getOwner()->getTranslation("brush.biome"));
				break;
			case 7:
				$player->sendMessage(TF::GREEN . $this->getOwner()->getTranslation("brush.biome") . TF::AQUA . $message[0]);
				$this->getOwner()->getPresetManager()->addToCreationData($player, "biome", strtolower($message[0]));
				$player->sendMessage(TF::GRAY . $this->getOwner()->getTranslation("brush.obsolete"));
				break;
			case 8:
				$player->sendMessage(TF::GREEN . $this->getOwner()->getTranslation("brush.obsolete") . TF::AQUA . $message[0]);
				$this->getOwner()->getPresetManager()->addToCreationData($player, "obsolete", explode(",", strtolower($message[0])));
				$player->sendMessage(TF::GRAY . $this->getOwner()->getTranslation("brush.blocks"));
				break;
			case 9:
				$player->sendMessage(TF::GREEN . $this->getOwner()->getTranslation("brush.blocks") . TF::AQUA . $message[0]);
				$this->getOwner()->getPresetManager()->addToCreationData($player, "blocks", explode(",", strtolower($message[0])));
				$this->getOwner()->getPresetManager()->parsePresetCreationInfo($player, $this->getOwner()->getPresetManager()->getCreationData($player, "name"));
				$player->sendMessage(TF::GREEN . "Preset creation process finished successfully.");
				break;
		}
		$event->setCancelled();
		return true;
	}
	
	public function getOwner(): Loader {
		return $this->main;
	}
}