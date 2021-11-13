<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\listener;

use BlockHorizons\BlockSniper\brush\Brush;
use BlockHorizons\BlockSniper\brush\TargetHighlight;
use BlockHorizons\BlockSniper\data\Translation;
use BlockHorizons\BlockSniper\Loader;
use BlockHorizons\BlockSniper\session\PlayerSession;
use BlockHorizons\BlockSniper\session\SessionManager;
use BlockHorizons\BlockSniper\task\CooldownBarTask;
use BlockHorizons\BlockSniper\task\SessionDeletionTask;
use BlockHorizons\BlockSniper\ui\window\BrushMenuWindow;
use MyPlot\PlotLevelSettings;
use pocketmine\block\Block;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerItemHeldEvent;
use pocketmine\event\player\PlayerItemUseEvent;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\math\Vector2;
use pocketmine\nbt\tag\StringTag;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use pocketmine\entity\Location;
use pocketmine\world\Position;
use Sandertv\Marshal\Unmarshal;

class BrushListener implements Listener{

	public const KEY_BRUSH_UUID = "blocksniper:brush_uuid";

	/** @var Brush[] */
	public static $brushItems = [];

	/** @var Loader */
	private $loader = null;
	/** @var TargetHighlight[] */
	private $targetHighlights = [];

	public function __construct(Loader $loader){
		$this->loader = $loader;

		if(file_exists($loader->getDataFolder() . "bound_brushes.json")){
			$data = json_decode(file_get_contents($loader->getDataFolder() . "bound_brushes.json"), true);
			if($data === null){
				return;
			}
			foreach($data as $uuid => $brushData){
				$b = new Brush();
				if(is_string($brushData)){
					Unmarshal::json($brushData, $b);
				}
				self::$brushItems[$uuid] = $b;
			}
		}
	}

	public function saveBrushes() : void{
		$data = [];
		foreach(self::$brushItems as $uuid => $brush){
			$data[$uuid] = json_encode($brush);
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
			case($brushUuidTag = $hand->getNamedTag()->getTag(self::KEY_BRUSH_UUID)) instanceof StringTag:
				$uuid = $brushUuidTag->getValue();
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
		$event->cancel();
	}

	private function useBrush(PlayerSession $session, Brush $brush, Player $player) : void{
		if($brush->mode === Brush::MODE_SELECTION && !$session->getSelection()->ready()){
			$player->sendMessage(
				TextFormat::RED . Translation::get(Translation::COMMANDS_COMMON_WARNING_PREFIX) .
				Translation::get(Translation::BRUSH_SELECTION_ERROR)
			);

			return;
		}
		$startTime = microtime(true);

		$selection = $brush->mode === Brush::MODE_BRUSH ? null : $session->getSelection();
		if($brush->execute($session, $session->getTargetBlock(), $this->getPlotPoints($player), $selection)){
			// If the brush was executed synchronously, we send a cooldown bar task directly.
			$duration = round(microtime(true) - $startTime, 2);
			$player->sendPopup(TextFormat::GREEN . Translation::get(Translation::BRUSH_STATE_DONE) . " ($duration seconds)");
			$this->loader->getScheduler()->scheduleRepeatingTask(new CooldownBarTask($this->loader, $brush, $player), 1);
		}
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
		$vec = $block->getPosition()->asVector3();
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
			$event->cancel();
		}
	}

	/**
	 * @param BlockBreakEvent $event
	 */
	public function onBlockBreak(BlockBreakEvent $event) : void{
		if($this->selection($event->getPlayer(), $event->getBlock(), PlayerInteractEvent::LEFT_CLICK_BLOCK)){
			$event->cancel();
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
		$settings = $this->loader->getMyPlot()->getLevelSettings($player->getWorld()->getFolderName());
		if($settings === null){
			if($player->hasPermission("blocksniper-myplot.allow-outside")){
				return [];
			}

			return [[new Vector2(0, 0), new Vector2(0, 0)]];
		}
		$plotSize = $settings->plotSize;
		foreach($this->loader->getMyPlot()->getPlotsOfPlayer($player->getName(), $player->getWorld()->getFolderName()) as $plot){
			$minVec = new Vector2($this->calcActual($plot->X, $settings) - $plotSize, $this->calcActual($plot->Z, $settings) - $plotSize);
			$maxVec = new Vector2($this->calcActual($plot->X, $settings) - 1, $this->calcActual($plot->Z, $settings) - 1);
			$plotPoints[] = [$minVec, $maxVec];
		}
		if(empty($plotPoints)){
			return [[new Vector2(0, 0), new Vector2(0, 0)]];
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
		if(!$event->getItem()->equals($this->loader->config->brushItem->parse())){
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
		$player = $event->getPlayer();
		if(!SessionManager::playerSessionExists($player->getName())){
			return;
		}
		if(isset($this->targetHighlights[$player->getName()])){
			$entity = $this->targetHighlights[$player->getName()];
			$entity->close();
			unset($this->targetHighlights[$player->getName()]);
		}

		$this->loader->getScheduler()->scheduleDelayedTask(
			new SessionDeletionTask($this->loader, SessionManager::getPlayerSession($player)),
			$this->loader->config->sessionTimeoutTime * 20 * 60
		);
	}

	public function onMove(PlayerMoveEvent $event) : void{
		$player = $event->getPlayer();
		if(!$player->hasPermission("blocksniper.command.brush")){
			// The player does not have permission to brush, so we don't need to highlight the target block of the
			// player.
			return;
		}

		$brushItem = $this->loader->config->brushItem->parse();
		$name = $player->getName();
		$hand = $player->getInventory()->getItemInHand();
		if(!$hand->equals($brushItem) && !($hand->getNamedTag()->getTag(BrushListener::KEY_BRUSH_UUID) instanceof StringTag)){
			if(isset($this->targetHighlights[$name])){
				// The player still had a target highlight entity active, so we need to remove that as the player
				// is no longer holding the brush item.
				$entity = $this->targetHighlights[$name];
				$entity->close();
				unset($this->targetHighlights[$name]);
			}

			// The player isn't holding the brush item, so no need to highlight either.
			return;
		}
		$this->highlightTarget($player);
	}

	/**
	 * @param Player $player
	 */
	public function highlightTarget(Player $player) : void{
		$name = $player->getName();

		$pos = SessionManager::getPlayerSession($player)->getTargetBlock()->add(0.0, 0, 1.0)->subtract(0.04, 0.04, -0.04);
		$loc = Location::fromObject($pos, $player->getWorld());
		if(!isset($this->targetHighlights[$name])){
			$this->targetHighlights[$name] = new TargetHighlight(new Position($player->getPosition()->x, 0, $player->getPosition()->z, $loc->getWorld()));
			$this->targetHighlights[$name]->spawnTo($player);
		}
		$this->targetHighlights[$player->getName()]->teleport($loc);
	}
}
