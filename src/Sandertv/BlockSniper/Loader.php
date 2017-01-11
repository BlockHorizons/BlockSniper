<?php

namespace Sandertv\BlockSniper;

use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat as TF;
use Sandertv\BlockSniper\commands\BrushWandCommand;
use Sandertv\BlockSniper\commands\SnipeCommand;
use Sandertv\BlockSniper\commands\UndoCommand;
use Sandertv\BlockSniper\listeners\EventListener;
use Sandertv\BlockSniper\UndoStorer;

class Loader extends PluginBase {
	
	public $brushwand = [];
	public $undoStore;
	
	public function onEnable() {
		
		$this->getLogger()->info(TF::GREEN . "BlockSniper has been enabled");
		
		if(!is_dir($this->getDataFolder())) {
			mkdir($this->getDataFolder());
		}
		$this->saveResource("settings.yml");
		$this->settings = new Config($this->getDataFolder() . "settings.yml", Config::YAML);
		
		$this->getServer()->getCommandMap()->register("snipe", new SnipeCommand($this));
		$this->getServer()->getCommandMap()->register("brushwand", new BrushWandCommand($this));
		$this->getServer()->getCommandMap()->register("undo", new UndoCommand($this));
		
		$this->getServer()->getPluginManager()->registerEvents(new EventListener($this), $this);
		
		$this->undoStore = new UndoStorer($this);
	}
	
	public function onDisable() {
		
		$this->getLogger()->info(TF::RED . "BlockSniper has been disabled");
		
		$this->getUndoStore()->resetUndoStorage();
		
	}
	
	public function getUndoStore(): UndoStorer {
		return $this->undoStore;
	}
	
	/**
	 * @param Player $player
	 * @param string $type
	 * @param int    $radius
	 * @param string $blocks
	 * @param        $data = null
	 */
	public function enableBrushWand(Player $player, string $type, $radius, string $blocks = null, $data = null) {
		$this->brushwand[$player->getName()] = [
			"type" => $type,
			"radius" => $radius,
			"additionalData" => $data,
			"blocks" => $blocks];
	}
	
	/**
	 * @param Player $player
	 *
	 * @return array
	 */
	public function getBrushWand(Player $player) {
		return $this->brushwand[$player->getName()];
	}
	
	/**
	 * @param Player $player
	 */
	public function disableBrushWand(Player $player) {
		unset($this->brushwand[$player->getName()]);
		$player->sendMessage(TF::YELLOW . "Brush wand disabled.");
	}
	
	/**
	 * @param Player $player
	 *
	 * @return boolean
	 */
	public function hasBrushWandEnabled(Player $player): bool {
		if(isset($this->brushwand[$player->getName()])) {
			return true;
		}
		return false;
	}
	
}
