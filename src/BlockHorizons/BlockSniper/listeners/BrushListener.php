<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockSniper\listeners;

use BlockHorizons\BlockSniper\Loader;
use BlockHorizons\BlockSniper\sessions\SessionManager;
use BlockHorizons\BlockSniper\ui\WindowHandler;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerItemHeldEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\network\mcpe\protocol\ModalFormRequestPacket;

class BrushListener implements Listener {

	/** @var Loader */
	private $loader = null;
	/** @var array */
	private $cancelWindow = [];

	public function __construct(Loader $loader) {
		$this->loader = $loader;
	}

	/**
	 * @param PlayerInteractEvent $event
	 *
	 * @return bool
	 */
	public function brush(PlayerInteractEvent $event): bool {
		$player = $event->getPlayer();
		if($player->getInventory()->getItemInHand()->getId() === $this->getLoader()->getSettings()->getBrushItem()) {
			if($player->hasPermission("blocksniper.command.brush")) {
				$brush = SessionManager::getPlayerSession($player)->getBrush();
				$brush->execute($player);
				$event->setCancelled();
			}
		}
		return false;
	}

	/**
	 * @param PlayerItemHeldEvent $event
	 *
	 * @return bool
	 */
	public function onItemHeld(PlayerItemHeldEvent $event): bool {
		$player = $event->getPlayer();
		if($event->getItem()->getId() === $this->getLoader()->getSettings()->getBrushItem()) {
			if($player->hasPermission("blocksniper.command.brush")) {
				if(isset($this->cancelWindow[$player->getLowerCaseName()])) {
					if(time() - $this->cancelWindow[$player->getLowerCaseName()] < 2) {
						return false;
					}
				}
				$windowHandler = new WindowHandler();
				$packet = new ModalFormRequestPacket();
				$packet->formId = $windowHandler->getWindowIdFor(WindowHandler::WINDOW_BRUSH_MENU);
				$packet->formData = $windowHandler->getWindowJson(WindowHandler::WINDOW_BRUSH_MENU, $this->getLoader(), $player);
				$player->dataPacket($packet);
				return true;
			}
		}
		return false;
	}

	/**
	 * @param PlayerJoinEvent $event
	 *
	 * @return bool
	 */
	public function onJoin(PlayerJoinEvent $event): bool {
		$this->cancelWindow[$event->getPlayer()->getLowerCaseName()] = time();
		return true;
	}

	/**
	 * @return Loader
	 */
	public function getLoader(): Loader {
		return $this->loader;
	}
}
