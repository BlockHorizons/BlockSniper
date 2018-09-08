<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\listeners;

use BlockHorizons\BlockSniper\Loader;
use BlockHorizons\BlockSniper\sessions\SessionManager;
use BlockHorizons\BlockSniper\tasks\SessionDeletionTask;
use BlockHorizons\BlockSniper\ui\windows\BrushMenuWindow;
use MyPlot\PlotLevelSettings;
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
	public function brush(PlayerInteractEvent $event) : void{
		$player = $event->getPlayer();
		$hand = $player->getInventory()->getItemInHand();
		$brush = $this->loader->config->brushItem->parse();
		if($hand->getId() === $brush->getId() && $hand->getDamage() === $brush->getDamage()){
			if($player->hasPermission("blocksniper.command.brush")){
				$brush = ($session = SessionManager::getPlayerSession($player))->getBrush();
				$brush->execute($session, $this->getPlotPoints($player));
				$event->setCancelled();
			}
		}
	}

	/**
	 * @param Player $player
	 *
	 * @return Vector2[][]
	 */
	public function getPlotPoints(Player $player) : array{
		if($player->hasPermission("blocksniper-myplot.bypass") || !$this->loader->isMyPlotAvailable()){
			return [];
		}
		$plotPoints = [];
		$settings = $this->loader->getMyPlot()->getLevelSettings($player->getLevel()->getName());
		if($settings === null){
			if($player->hasPermission("blocksniper-myplot.allow-outside")){
				return [];
			}

			return [[new Vector2(), new Vector2()]];
		}
		$plotSize = $settings->plotSize;
		foreach($this->loader->getMyPlot()->getPlotsOfPlayer($player->getName(), $player->getLevel()->getFolderName()) as $plot){
			$minVec = new Vector2($this->calcActual($plot->X, $settings) - $plotSize, $this->calcActual($plot->Z, $settings) - $plotSize);
			$maxVec = new Vector2($this->calcActual($plot->X, $settings) - 1, $this->calcActual($plot->Z, $settings) - 1);
			$plotPoints[] = [$minVec, $maxVec];
		}
		if(empty($plotPoints)){
			return [[new Vector2(), new Vector2()]];
		}

		return $plotPoints;
	}

	/**
	 * @param int               $coordinate
	 * @param PlotLevelSettings $settings
	 *
	 * @return int
	 */
	private function calcActual(int $coordinate, PlotLevelSettings $settings) : int{
		$coordinate += 1;

		return $coordinate * $settings->plotSize + ($coordinate - 1) * $settings->roadWidth;
	}

	/**
	 * @param PlayerItemHeldEvent $event
	 */
	public function onItemHeld(PlayerItemHeldEvent $event) : void{
		$player = $event->getPlayer();
		$brush = $this->loader->config->brushItem->parse();
		if($event->getItem()->getId() === $brush->getId() && $event->getItem()->getDamage() === $brush->getDamage()){
			if($player->hasPermission("blocksniper.command.brush")){
				$player->sendForm(new BrushMenuWindow($this->loader, $player));
			}
		}
	}

	/**
	 * @param PlayerQuitEvent $event
	 */
	public function onQuit(PlayerQuitEvent $event) : void{
		$this->loader->getScheduler()->scheduleDelayedTask(
			new SessionDeletionTask($this->loader, SessionManager::getPlayerSession($event->getPlayer())),
			$this->loader->config->sessionTimeoutTime * 20 * 60
		);
	}
}
