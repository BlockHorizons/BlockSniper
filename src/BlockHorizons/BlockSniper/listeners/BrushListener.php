<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\listeners;

use BlockHorizons\BlockSniper\brush\Brush;
use BlockHorizons\BlockSniper\data\Translation;
use BlockHorizons\BlockSniper\Loader;
use BlockHorizons\BlockSniper\sessions\PlayerSession;
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
use pocketmine\nbt\tag\StringTag;
use pocketmine\Player;
use pocketmine\utils\TextFormat;
use Sandertv\Marshal\Unmarshal;

class BrushListener implements Listener{

	public const KEY_BRUSH_UUID = "blocksniper:brush_uuid";

	/** @var Brush[] */
	public static $brushItems = [];

	/** @var Loader */
	private $loader = null;

	public function __construct(Loader $loader){
		$this->loader = $loader;

		if(file_exists($loader->getDataFolder() . "bound_brushes.json")){
			$data = json_decode(file_get_contents($loader->getDataFolder() . "bound_brushes.json"), true);
			foreach($data as $uuid => $brushData){
				$b = new Brush($brushData["player"]);
				Unmarshal::json($brushData["data"], $b);
				self::$brushItems[$uuid] = $b;
			}
		}
	}

	public function saveBrushes() : void {
		$data = [];
		foreach(self::$brushItems as $uuid => $brush){
			$data[$uuid] = ["player" => $brush->player, "data" => json_encode($brush)];
		}
		file_put_contents($this->loader->getDataFolder() . "bound_brushes.json", json_encode($data));
	}

	/**
	 * @param PlayerItemUseEvent $event
	 */
	public function brush(PlayerItemUseEvent $event) : void{
		$player = $event->getPlayer();
		if(!$player->hasPermission("blocksniper.command.brush")){
			return;
		}

		$hand = $event->getItem();
		switch(true){
			case $hand->equals($this->loader->config->brushItem->parse()):
				$session = SessionManager::getPlayerSession($player);
				$this->useBrush($session, $session->getBrush(), $player);
				break;
			case $hand->getNamedTag()->hasTag(self::KEY_BRUSH_UUID, StringTag::class):
				$uuid = $hand->getNamedTag()->getString(self::KEY_BRUSH_UUID);
				$session = SessionManager::getPlayerSession($player);
				if(!isset(self::$brushItems[$uuid])){
					$this->loader->getLogger()->debug("Invalid bound brush found, removing the item: " . $uuid);
					$hand->getNamedTag()->removeTag(self::KEY_BRUSH_UUID);
					$player->getInventory()->setItemInHand($hand);
					return;
				}
				$this->useBrush($session, self::$brushItems[$uuid], $player);
				break;
			default:
				return;
		}
		$event->setCancelled();
	}

	private function useBrush(PlayerSession $session, Brush $brush, Player $player) : void{
		if($brush->mode === Brush::MODE_SELECTION && !$session->getSelection()->ready()){
			$player->sendMessage(
				TextFormat::RED . Translation::get(Translation::COMMANDS_COMMON_WARNING_PREFIX) .
				Translation::get(Translation::BRUSH_SELECTION_ERROR)
			);
			return;
		}
		$selection = $brush->mode === Brush::MODE_BRUSH ? null : $session->getSelection();
		$brush->execute($session, $selection, $this->getPlotPoints($player));
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
		$hand = $player->getInventory()->getItemInHand();
		if(!$hand->equals($this->loader->config->selectionItem->parse())){
			return false;
		}

		$selection = ($session = SessionManager::getPlayerSession($player))->getSelection();
		$vec = $block->asVector3();
		[$x, $y, $z] = [$vec->x, $vec->y, $vec->z];
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
		$settings = $this->loader->getMyPlot()->getLevelSettings($player->getLevel()->getFolderName());
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
		if(!$event->getItem()->equals($this->loader->config->brushItem->parse())) {
			return;
		}
		if($player->hasPermission("blocksniper.command.brush")){
			$player->sendForm(new BrushMenuWindow($this->loader, $player, SessionManager::getPlayerSession($player)->getBrush()));
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
