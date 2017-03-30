<?php

namespace Sandertv\BlockSniper\listeners;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\Player;
use pocketmine\utils\TextFormat as TF;
use Sandertv\BlockSniper\brush\BrushManager;
use Sandertv\BlockSniper\events\BrushUseEvent;
use Sandertv\BlockSniper\Loader;

class BrushListener implements Listener {
	
	private $owner;
	
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
				
				$this->getOwner()->getBrushManager()->createBrush($player);
				$shape = BrushManager::get($player)->getShape();
				$type = BrushManager::get($player)->getType($shape->getBlocksInside());
				
				$this->getOwner()->getServer()->getPluginManager()->callEvent($event = new BrushUseEvent($this->getOwner(), $player, $shape, $type));
				if($event->isCancelled()) {
					return false;
				}
				
				$type->fillShape();
				$this->decrementBrush($player);
				
				$event->setCancelled();
			}
		}
		return true;
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
		if(BrushManager::get($player)->isDecrementing()) {
			if(BrushManager::get($player)->getSize() <= 1) {
				if($this->getOwner()->getSettings()->get("Reset-Decrement-Brush") !== false) {
					BrushManager::get($player)->setSize(BrushManager::get($player)->resetSize[$player->getId()]);
					$player->sendPopup(TF::GREEN . "Brush reset to original size.");
					return true;
				}
				return false;
			}
			BrushManager::get($player)->setSize(BrushManager::get($player)->getSize() - 1);
			return true;
		}
		return false;
	}
}
