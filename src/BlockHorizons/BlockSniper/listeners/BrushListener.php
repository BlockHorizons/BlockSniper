<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\listeners;

use BlockHorizons\BlockSniper\brush\Brush;
use BlockHorizons\BlockSniper\data\Translation;
use BlockHorizons\BlockSniper\Loader;
use BlockHorizons\BlockSniper\sessions\SessionManager;
use BlockHorizons\BlockSniper\tasks\SessionDeletionTask;
use BlockHorizons\BlockSniper\ui\windows\BrushMenuWindow;
use MyPlot\PlotLevelSettings;
use pocketmine\block\Block;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerItemHeldEvent;
use pocketmine\event\player\PlayerItemUseEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\math\Vector2;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class BrushListener implements Listener{

	/** @var Loader */
	private $loader = null;
	/** @var int */
	private $lastSelection = 0;

	public function __construct(Loader $loader){
		$this->loader = $loader;
	}

	/**
	 * @param PlayerItemUseEvent $event
	 */
	public function brush(PlayerItemUseEvent $event) : void{
		$player = $event->getPlayer();
		if(!$player->hasPermission("blocksniper.command.brush")){
			return;
		}
		$hand = $player->getInventory()->getItemInHand();
		$brushItem = $this->loader->config->brushItem->parse();
		if($hand->getId() !== $brushItem->getId() || $hand->getDamage() !== $brushItem->getDamage()){
			return;
		}

		$brush = ($session = SessionManager::getPlayerSession($player))->getBrush();
		if($brush->mode === Brush::MODE_SELECTION && !$session->getSelection()->ready()){
			$player->sendMessage(
				TextFormat::RED . Translation::get(Translation::COMMANDS_COMMON_WARNING_PREFIX) .
				Translation::get(Translation::BRUSH_SELECTION_ERROR)
			);

			return;
		}

		$selection = $brush->mode === Brush::MODE_BRUSH ? null : $session->getSelection();

		$brush->execute($session, $selection, $this->getPlotPoints($player));
		$event->setCancelled();
	}

	/**
	 * @param Player $player
	 * @param Block  $block
	 * @param int    $action
	 *
	 * @return bool
	 */
	private function selection(Player $player, Block $block, int $action) : bool{
		if(!$player->hasPermission("blocksniper.command.brush")){
			return false;
		}
		$selectionItem = $this->loader->config->selectionItem->parse();
		$hand = $player->getInventory()->getItemInHand();
		if($hand->getId() !== $selectionItem->getId() || $hand->getDamage() !== $selectionItem->getDamage()){
			return false;
		}
		if(time() - $this->lastSelection < 2){
			return false;
		}

		$selection = ($session = SessionManager::getPlayerSession($player))->getSelection();
		$vec = $block->asVector3();
		[$x, $y, $z] = [$vec->x, $vec->y, $vec->z];
		$this->lastSelection = time();
		switch($action){
			case PlayerInteractEvent::RIGHT_CLICK_BLOCK:
				$selection->setFirstPos($vec);
				$msg = Translation::get(Translation::BRUSH_SELECTION_FIRST) . " ($x, $y, $z)";
				$player->sendMessage(TextFormat::GREEN . $msg);

				return true;
			case PlayerInteractEvent::LEFT_CLICK_BLOCK:
				$selection->setSecondPos($vec);
				$msg = Translation::get(Translation::BRUSH_SELECTION_SECOND) . " ($x, $y, $z)";
				$player->sendMessage(TextFormat::GREEN . $msg);

				return true;
		}

		return false;
	}

	/**
	 * @param PlayerInteractEvent $event
	 */
	public function onBlockClick(PlayerInteractEvent $event) : void{
		if($this->selection($event->getPlayer(), $event->getBlock(), $event->getAction())){
			$event->setCancelled();
		}
	}

	/**
	 * @param BlockBreakEvent $event
	 */
	public function onBlockBreak(BlockBreakEvent $event) : void{
		if($this->selection($event->getPlayer(), $event->getBlock(), PlayerInteractEvent::LEFT_CLICK_BLOCK)){
			$event->setCancelled();
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
		if($event->getItem()->getId() !== $brush->getId() || $event->getItem()->getDamage() !== $brush->getDamage()) {
			return;
		}
		if($player->hasPermission("blocksniper.command.brush")){
			$player->sendForm(new BrushMenuWindow($this->loader, $player));
		}
	}

	/**
	 * @param PlayerQuitEvent $event
	 */
	public function onQuit(PlayerQuitEvent $event) : void{
		if(!SessionManager::playerSessionExists($event->getPlayer()->getName())){
			return;
		}
		$this->loader->getScheduler()->scheduleDelayedTask(
			new SessionDeletionTask($this->loader, SessionManager::getPlayerSession($event->getPlayer())),
			$this->loader->config->sessionTimeoutTime * 20 * 60
		);
	}
}
