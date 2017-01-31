<?php

namespace Sandertv\BlockSniper\listeners;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\item\Item;
use pocketmine\utils\TextFormat as TF;
use Sandertv\BlockSniper\brush\Brush;
use Sandertv\BlockSniper\Loader;
use Sandertv\BlockSniper\events\BrushUseEvent;

class EventListener implements Listener {
	
	public $owner;
	
	public function __construct(Loader $owner) {
		$this->owner = $owner;
	}
	
	public function onBrush(PlayerInteractEvent $event) {
		$player = $event->getPlayer();
		if($player->getInventory()->getItemInHand()->getId() === Item::GOLDEN_CARROT) {
			if($player->hasPermission("blocksniper.command.brush")) {
				$center = $player->getTargetBlock(100);
				
				if(!$center) {
					$player->sendMessage(TF::RED . "[Warning] " . $this->getOwner()->getTranslation("commands.errors.no-target-found"));
					return;
				}
				
				$this->getOwner()->getServer()->getPluginManager()->callEvent($event = new BrushUseEvent($this->getOwner(), $player));
				if($event->isCancelled()) {
					return;
				}
				
				$shape = Brush::getShape($player);
				$type = Brush::getType($player, $shape->getBlocksInside());
				
				$type->fillShape();
				return true;
			}
		}
	}
	
	public function getOwner(): Loader {
		return $this->owner;
	}
}
