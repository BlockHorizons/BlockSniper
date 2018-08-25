<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\listeners;

use BlockHorizons\BlockSniper\Loader;
use BlockHorizons\BlockSniper\sessions\SessionManager;
use BlockHorizons\BlockSniper\ui\WindowHandler;
use BlockHorizons\BlockSniper\ui\windows\BrushMenuWindow;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerItemHeldEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\math\Vector2;
use pocketmine\Player;

class BrushListener implements Listener{

	/** @var Loader */
	private $loader = null;

	public function __construct(Loader $loader){
		$this->loader = $loader;
	}

	/**
	 * @param PlayerInteractEvent $event
	 *
	 * @return bool
	 */
	public function brush(PlayerInteractEvent $event) : bool{
		$player = $event->getPlayer();
		$hand = $player->getInventory()->getItemInHand();
		$brush = $this->loader->config->BrushItem->parse();
		if($hand->getId() === $brush->getId() && $hand->getDamage() === $brush->getDamage()){
			if($player->hasPermission("blocksniper.command.brush")){
				if(!SessionManager::playerSessionExists($player)){
					SessionManager::createPlayerSession($player, $this->loader);
				}
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
	public function getPlotPoints(Player $player) : array{
		if($player->hasPermission("blocksniper-myplot-bypass") || !$this->loader->isMyPlotAvailable()){
			return [];
		}
		$plotPoints = [];
		$settings = $this->loader->getMyPlot()->getLevelSettings($player->getLevel()->getName());
		if($settings === null){
			return [[new Vector2(), new Vector2()]];
		}
		$plotSize = $settings->plotSize;
		foreach($this->loader->getMyPlot()->getPlotsOfPlayer($player->getName(), $player->getLevel()->getFolderName()) as $plot){
			$minVec = new Vector2($plot->X, $plot->Z);
			$maxVec = new Vector2($plot->X + $plotSize, $plot->Z + $plotSize);
			$plotPoints[] = [$minVec, $maxVec];
		}
		if(empty($plotPoints)){
			return [[new Vector2(), new Vector2()]];
		}

		return $plotPoints;
	}

	/**
	 * @param PlayerItemHeldEvent $event
	 *
	 * @return bool
	 */
	public function onItemHeld(PlayerItemHeldEvent $event) : bool{
		$player = $event->getPlayer();
		$brush = $this->loader->config->BrushItem->parse();
		if($event->getItem()->getId() === $brush->getId() && $event->getItem()->getDamage() === $brush->getDamage()){
			if($player->hasPermission("blocksniper.command.brush")){
				if(!SessionManager::playerSessionExists($player)){
					SessionManager::createPlayerSession($player, $this->loader);
				}
				$player->sendForm(new BrushMenuWindow($this->loader, $player));

				return true;
			}
		}

		return false;
	}

	/**
	 * @param PlayerQuitEvent $event
	 *
	 * @return bool
	 */
	public function onQuit(PlayerQuitEvent $event) : bool{
		SessionManager::closeSession($event->getPlayer());

		return true;
	}
}
