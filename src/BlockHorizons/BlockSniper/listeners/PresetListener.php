<?php

namespace BlockHorizons\BlockSniper\listeners;

use BlockHorizons\BlockSniper\brush\BaseShape;
use BlockHorizons\BlockSniper\brush\BaseType;
use BlockHorizons\BlockSniper\events\PresetCreationEvent;
use BlockHorizons\BlockSniper\Loader;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\utils\TextFormat as TF;

class PresetListener implements Listener {
	
	private $loader;
	
	public function __construct(Loader $loader) {
		$this->loader = $loader;
	}
	
	public function onChat(PlayerChatEvent $event) {
		$player = $event->getPlayer();
		if(!$this->getLoader()->getPresetManager()->isCreatingAPreset($player)) {
			return false;
		}
		$message = explode(" ", $event->getMessage());
		
		if(strtolower($message[0]) === "cancel") {
			$this->getLoader()->getPresetManager()->cancelPresetCreationProcess($player);
			$player->sendMessage(TF::YELLOW . $this->getLoader()->getTranslation("commands.succeed.preset.canceled"));
			$event->setCancelled();
			return true;
		}
		
		// Ew... I know.
		switch($this->getLoader()->getPresetManager()->getCurrentPresetCreationProgress($player)) {
			case 0:
				$player->sendMessage(TF::AQUA . $message[0]);
				$this->getLoader()->getPresetManager()->addToCreationData($player, "name", $message[0]);
				$player->sendMessage(TF::GRAY . $this->getLoader()->getTranslation("brush.shape"));
				break;
			case 1:
				if(!BaseShape::isShape(strtolower($message[0]))) {
					$player->sendMessage(TF::RED . "[Warning] " . $this->getLoader()->getTranslation("commands.errors.shape-not-found"));
					return false;
				}
				$player->sendMessage(TF::GREEN . $this->getLoader()->getTranslation("brush.shape") . TF::AQUA . $message[0]);
				$this->getLoader()->getPresetManager()->addToCreationData($player, "shape", strtolower($message[0]));
				$player->sendMessage(TF::GRAY . $this->getLoader()->getTranslation("brush.type"));
				break;
			case 2:
				if(!BaseType::isType(strtolower($message[0]))) {
					$player->sendMessage(TF::RED . "[Warning] " . $this->getLoader()->getTranslation("commands.errors.shape-not-found"));
					return false;
				}
				$player->sendMessage(TF::GREEN . $this->getLoader()->getTranslation("brush.type") . TF::AQUA . $message[0]);
				$this->getLoader()->getPresetManager()->addToCreationData($player, "type", strtolower($message[0]));
				$player->sendMessage(TF::GRAY . $this->getLoader()->getTranslation("brush.decrement"));
				break;
			case 3:
				$message[0] = (bool)$message[0];
				if(!is_bool($message[0])) {
					return false;
				}
				$player->sendMessage(TF::GREEN . $this->getLoader()->getTranslation("brush.decrement") . TF::AQUA . $message[0]);
				$this->getLoader()->getPresetManager()->addToCreationData($player, "decrement", $message[0]);
				$player->sendMessage(TF::GRAY . $this->getLoader()->getTranslation("brush.perfect"));
				break;
			case 4:
				$message[0] = (bool)$message[0];
				if(!is_bool($message[0])) {
					return false;
				}
				$player->sendMessage(TF::GREEN . $this->getLoader()->getTranslation("brush.perfect") . TF::AQUA . $message[0]);
				$this->getLoader()->getPresetManager()->addToCreationData($player, "perfect", $message[0]);
				$player->sendMessage(TF::GRAY . $this->getLoader()->getTranslation("brush.size"));
				break;
			case 5:
				if(!is_numeric($message[0])) {
					return false;
				}
				if($message[0] > $this->getLoader()->getSettings()->get("Maximum-Radius")) {
					$player->sendMessage(TF::RED . "[Warning] " . $this->getLoader()->getTranslation("commands.errors.radius-too-big"));
					return false;
				}
				$player->sendMessage(TF::GREEN . $this->getLoader()->getTranslation("brush.size") . TF::AQUA . $message[0]);
				$this->getLoader()->getPresetManager()->addToCreationData($player, "size", $message[0]);
				$player->sendMessage(TF::GRAY . $this->getLoader()->getTranslation("brush.hollow"));
				break;
			case 6:
				$message[0] = (bool)$message[0];
				if(!is_bool($message[0])) {
					return false;
				}
				$player->sendMessage(TF::GREEN . $this->getLoader()->getTranslation("brush.hollow") . TF::AQUA . $message[0]);
				$this->getLoader()->getPresetManager()->addToCreationData($player, "hollow", $message[0]);
				$player->sendMessage(TF::GRAY . $this->getLoader()->getTranslation("brush.height"));
				break;
			case 7:
				if(!is_numeric($message[0])) {
					return false;
				}
				if($message[0] > $this->getLoader()->getSettings()->get("Maximum-Radius")) {
					$player->sendMessage(TF::RED . "[Warning] " . $this->getLoader()->getTranslation("commands.errors.radius-too-big"));
					return false;
				}
				$player->sendMessage(TF::GREEN . $this->getLoader()->getTranslation("brush.height") . TF::AQUA . $message[0]);
				$this->getLoader()->getPresetManager()->addToCreationData($player, "height", $message[0]);
				$player->sendMessage(TF::GRAY . $this->getLoader()->getTranslation("brush.biome"));
				break;
			case 8:
				$player->sendMessage(TF::GREEN . $this->getLoader()->getTranslation("brush.biome") . TF::AQUA . $message[0]);
				$this->getLoader()->getPresetManager()->addToCreationData($player, "biome", strtolower($message[0]));
				$player->sendMessage(TF::GRAY . $this->getLoader()->getTranslation("brush.obsolete"));
				break;
			case 9:
				$player->sendMessage(TF::GREEN . $this->getLoader()->getTranslation("brush.obsolete") . TF::AQUA . $message[0]);
				$this->getLoader()->getPresetManager()->addToCreationData($player, "obsolete", explode(",", strtolower($message[0])));
				$player->sendMessage(TF::GRAY . $this->getLoader()->getTranslation("brush.blocks"));
				break;
			case 10:
				$player->sendMessage(TF::GREEN . $this->getLoader()->getTranslation("brush.blocks") . TF::AQUA . $message[0]);
				$this->getLoader()->getPresetManager()->addToCreationData($player, "blocks", explode(",", strtolower($message[0])));
				$this->getLoader()->getServer()->getPluginManager()->callEvent($event = new PresetCreationEvent($this->getLoader(), $player, $this->getLoader()->getPresetManager()->getCreationData($player)));
				if($event->isCancelled()) {
					unset($this->getLoader()->getPresetManager()->presetCreation[$player->getId()]);
					$player->sendMessage(TF::RED . "[Warning] Preset creation process cancelled.");
					break;
				}
				$this->getLoader()->getPresetManager()->parsePresetCreationInfo($player, $this->getLoader()->getPresetManager()->getCreationData($player, "name"));
				$player->sendMessage(TF::GREEN . "Preset creation process finished successfully.");
				break;
		}
		$event->setCancelled();
		return true;
	}
	
	public function getLoader(): Loader {
		return $this->loader;
	}
}