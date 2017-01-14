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

use Sandertv\BlockSniper\tasks\UndoDiminishTask;

class Loader extends PluginBase {
	
	public $brushwand = [];
	public $undoStore;
	public $settings;
	
	public $availableLanguages = [
		"en",
		"nl"
	];
	public $language;
	
	public function onEnable() {
		$this->getLogger()->info(TF::GREEN . "BlockSniper has been enabled.");
		$this->undoStore = new UndoStorer($this);
		if(!is_dir($this->getDataFolder())) {
			mkdir($this->getDataFolder());
		}
		$this->saveResource("settings.yml");
		$this->settings = new Config($this->getDataFolder() . "settings.yml", Config::YAML);
		
		// Language file setup
		if(!is_dir($this->getDataFolder() . "languages/")) {
			mkdir($this->getDataFolder() . "languages/");
		}
		if(!$this->setupLanguageFile()) {
			$this->getLogger()->info(TF::AQUA . "[BlockSniper] No valid language selected, English has been auto-selected.");
		}
		
		$this->registerCommands();
		$this->getServer()->getPluginManager()->registerEvents(new EventListener($this), $this);
	}
	
	public function onDisable() {
		$this->getLogger()->info(TF::RED . "BlockSniper has been disabled.");
		$this->getUndoStore()->resetUndoStorage();
	}
	
	public function registerCommands() {
		$this->getServer()->getCommandMap()->register("snipe", new SnipeCommand($this));
		$this->getServer()->getCommandMap()->register("brushwand", new BrushWandCommand($this));
		$this->getServer()->getCommandMap()->register("undo", new UndoCommand($this));
	}
	
	/**
	 * @return bool
	 */
	public function setupLanguageFile() {
		if(!file_exists($this->getDataFolder() . "language.yml")) {
			foreach($this->availableLanguages as $language) {
				if($this->getSettings()->get("Message-Language") === $language) {
					$this->saveResource("languages/" . $language . ".yml");
					$this->language = new Config($this->getDataFolder() . "languages/" . $language . ".yml", Config::YAML);
					return true;
				}
			}
		}
		$this->saveResource("languages/en.yml");
		$this->language = new Config($this->getDataFolder() . "languages/en.yml", Config::YAML);
		return false;
	}
	
	/**
	 * @param string $message
	 *
	 * @return string
	 */
	public function getTranslation(string $message) {
		return (string) $this->language->getNested($message);
	}
	
	public function scheduleTasks() {
		$this->getServer()->getScheduler()->scheduleDelayedTask(new UndoDiminishTask($this), 2400);
	}
	
	/**
	 * @return Config
	 */
	public function getSettings(): Config {
		return $this->settings;
	}
	
	/**
	 * @return UndoStorer
	 */
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
		$player->sendMessage(TF::YELLOW . $this->getTranslation("brushwand.disable"));
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
