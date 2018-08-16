<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockSniper\listeners;

use BlockHorizons\BlockSniper\Loader;
use BlockHorizons\BlockSniper\sessions\SessionManager;
use BlockHorizons\BlockSniper\ui\WindowHandler;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerItemHeldEvent;
use pocketmine\math\Vector2;
use pocketmine\network\mcpe\protocol\ModalFormRequestPacket;
use pocketmine\Player;

class BrushListener implements Listener {

	/** @var Loader */
	private $loader = null;

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
		$hand = $player->getInventory()->getItemInHand();
		$brush = $this->loader->config->BrushItem->parse();
		if($hand->getId() === $brush->getId() && $hand->getDamage() === $brush->getDamage()) {
			if($player->hasPermission("blocksniper.command.brush")) {
				$brush = ($session = SessionManager::getPlayerSession($player))->getBrush();
				$brush->execute($session, $this->getPlotPoints($player));
				$event->setCancelled();
			}
		}
		return false;
	}

	/**
	 * @param Player $player
	 *
	 * @return Vector2[][]
	 */
	public function getPlotPoints(Player $player): array {
		if($player->hasPermission("blocksniper-myplot-bypass") || !$this->loader->isMyPlotAvailable()) {
			return [];
		}
		$plotPoints = [];
		$settings = $this->loader->getMyPlot()->getLevelSettings($player->getLevel()->getName());
		if($settings === null) {
			return [[new Vector2(), new Vector2()]];
		}
		$plotSize = $settings->plotSize;
		foreach($this->loader->getMyPlot()->getPlotsOfPlayer($player->getName(), $player->getLevel()->getFolderName()) as $plot) {
			$minVec = new Vector2($plot->X, $plot->Z);
			$maxVec = new Vector2($plot->X + $plotSize, $plot->Z + $plotSize);
			$plotPoints[] = [$minVec, $maxVec];
		}
		if(empty($plotPoints)) {
			return [[new Vector2(), new Vector2()]];
		}
		return $plotPoints;
	}

	/**
	 * @param PlayerItemHeldEvent $event
	 *
	 * @return bool
	 */
	public function onItemHeld(PlayerItemHeldEvent $event): bool {
		$player = $event->getPlayer();
		$hand = $player->getInventory()->getItemInHand();
		$brush = $this->loader->config->BrushItem->parse();
		if($hand->getId() === $brush->getId() && $hand->getDamage() === $brush->getDamage()) {
			if($player->hasPermission("blocksniper.command.brush")) {
				$windowHandler = new WindowHandler();
				$packet = new ModalFormRequestPacket();
				$packet->formId = $windowHandler->getWindowIdFor(WindowHandler::WINDOW_BRUSH_MENU);
				$packet->formData = $windowHandler->getWindowJson(WindowHandler::WINDOW_BRUSH_MENU, $this->loader, $player);
				$player->sendDataPacket($packet);
				return true;
			}
		}
		return false;
	}
}
