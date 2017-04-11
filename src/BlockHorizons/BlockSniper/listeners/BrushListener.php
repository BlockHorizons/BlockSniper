<?php

namespace BlockHorizons\BlockSniper\listeners;

use BlockHorizons\BlockSniper\brush\BrushManager;
use BlockHorizons\BlockSniper\events\BrushUseEvent;
use BlockHorizons\BlockSniper\Loader;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\Player;
use pocketmine\utils\TextFormat as TF;

class BrushListener implements Listener {
	
	private $loader;
	
	public function __construct(Loader $loader) {
		$this->loader = $loader;
	}
	
	public function brush(PlayerInteractEvent $event) {
		$player = $event->getPlayer();
		if($player->getInventory()->getItemInHand()->getId() === (int)$this->getLoader()->getSettings()->get("Brush-Item")) {
			if($player->hasPermission("blocksniper.command.brush")) {
				$center = $player->getTargetBlock(100);
				
				if($center === null) {
					$player->sendMessage(TF::RED . "[Warning] " . $this->getLoader()->getTranslation("commands.errors.no-target-found"));
					return false;
				}
				
				$this->getLoader()->getBrushManager()->createBrush($player);
				$shape = BrushManager::get($player)->getShape();
				$type = BrushManager::get($player)->getType($shape->getBlocksInside());
				
				$this->getLoader()->getServer()->getPluginManager()->callEvent($event = new BrushUseEvent($this->getLoader(), $player, $shape, $type));
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
	public function getLoader(): Loader {
		return $this->loader;
	}
	
	/**
	 * @param Player $player
	 *
	 * @return bool
	 */
	public function decrementBrush(Player $player): bool {
		if(BrushManager::get($player)->isDecrementing()) {
			if(BrushManager::get($player)->getSize() <= 1) {
				if($this->getLoader()->getSettings()->get("Reset-Decrement-Brush") !== false) {
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
