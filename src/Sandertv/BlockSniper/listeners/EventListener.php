<?php

namespace Sandertv\BlockSniper\listeners;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\utils\TextFormat as TF;
use pocketmine\item\Item;
use Sandertv\BlockSniper\Loader;
use Sandertv\BlockSniper\brush\Brush;

class EventListener implements Listener {
	
	public $owner;
	
	public function __construct(Loader $owner) {
		$this->owner = $owner;
	}
	
	public function getOwner(): Loader {
		return $this->owner;
	}
	
	public function onBrush(PlayerInteractEvent $event) {
		$player = $event->getPlayer();
		if(!$player->getInventory()->getItemInHand()->getId() === Item::BRICK) {
			return;
		}
		
		if(!$player->hasPermission("blocksniper.command.brush")) {
			$player->sendMessage(TF::RED . "[Warning] " . $this->getOwner()->getTranslation("commands.errors.no-permission"));
			return;
		}
		
		$center = $player->getTargetBlock(100);
		
		if(!$center) {
			$player->sendMessage(TF::RED . "[Warning] " . $this->getOwner()->getTranslation("commands.errors.no-target-found"));
			return;
		}
		
		$shape = Brush::getShape($player);
		$type = Brush::getType($player, $shape->getBlocksInside());
		
		$type->fillShape();
		
		$player->sendPopup(TF::GREEN . $this->getOwner()->getTranslation("commands.succeed.default"));
		return true;
	}
}
