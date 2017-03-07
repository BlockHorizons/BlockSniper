<?php

namespace Sandertv\BlockSniper\listeners;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\Player;
use pocketmine\utils\TextFormat as TF;
use Sandertv\BlockSniper\brush\Brush;
use Sandertv\BlockSniper\events\BrushUseEvent;
use Sandertv\BlockSniper\Loader;

class EventListener implements Listener {
	
	public $owner;
	
	public function __construct(Loader $owner) {
		$this->owner = $owner;
	}
	
	public function brush(PlayerInteractEvent $event) {
		$player = $event->getPlayer();
		if($player->getInventory()->getItemInHand()->getId() === (int)$this->getOwner()->getSettings()->get("Brush-Item")) {
			if($player->hasPermission("blocksniper.command.brush")) {
				$center = $player->getTargetBlock(100);
				
				if($center === null) {
					$player->sendMessage(TF::RED . "[Warning] " . $this->getOwner()->getTranslation("commands.errors.no-target-found"));
					return false;
				}
				
				$this->getOwner()->getServer()->getPluginManager()->callEvent($event = new BrushUseEvent($this->getOwner(), $player));
				if($event->isCancelled()) {
					return false;
				}
				
				Brush::setupDefaultValues($player);
				
				$shape = Brush::getShape($player);
				$type = Brush::getType($player, $shape->getBlocksInside());
				
				$type->fillShape();
				$this->decrementBrush($player);
				return true;
			}
		}
	}
	
	/**
	 * @return Loader
	 */
	public function getOwner(): Loader {
		return $this->owner;
	}
	
	/**
	 * @param Player $player
	 *
	 * @return bool
	 */
	public function decrementBrush(Player $player): bool {
		if(Brush::isDecrementing($player)) {
			if(Brush::getSize($player) <= 1) {
				if($this->getOwner()->getSettings()->get("Reset-Decrement-Brush") !== false) {
					Brush::setSize($player, Brush::$resetSize[$player->getId()]);
					$player->sendPopup(TF::GREEN . "Brush reset to original size.");
					return true;
				}
				return false;
			}
			Brush::setSize($player, (Brush::getSize($player) - 1));
			return true;
		}
		return false;
	}
}
