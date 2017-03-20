<?php

namespace Sandertv\BlockSniper;

use pocketmine\plugin\PluginBase;
use pocketmine\utils\TextFormat as TF;
use Sandertv\BlockSniper\brush\Brush;
use Sandertv\BlockSniper\cloning\CloneStorer;
use Sandertv\BlockSniper\commands\BlockSniperCommand;
use Sandertv\BlockSniper\commands\BrushCommand;
use Sandertv\BlockSniper\commands\cloning\CloneCommand;
use Sandertv\BlockSniper\commands\cloning\PasteCommand;
use Sandertv\BlockSniper\commands\UndoCommand;
use Sandertv\BlockSniper\data\ConfigData;
use Sandertv\BlockSniper\data\TranslationData;
use Sandertv\BlockSniper\listeners\BrushListener;
use Sandertv\BlockSniper\listeners\PresetListener;
use Sandertv\BlockSniper\presets\PresetManager;
use Sandertv\BlockSniper\tasks\UndoDiminishTask;

class Loader extends PluginBase {
	
	const VERSION = "1.3.1";
	const API_TARGET = "2.0.0 - 3.0.0-ALPHA4";
	
	public $undoStore;
	public $cloneStore;
	public $settings;
	public $brush;
	public $presetManager;
	
	public $availableLanguages = [
		"en",
		"nl",
		"de",
		"fr",
		"fa",
		"ru",
		"zh_tw"
	];
	public $language;
	
	public function onEnable() {
		
		$this->reloadAll();
		
		$this->registerCommands();
		$this->registerListeners();
		
		$this->getServer()->getScheduler()->scheduleRepeatingTask(new UndoDiminishTask($this), 400);
	}
	
	public function reloadAll() {
		
		$this->saveResource("settings.yml");
		$this->settings = new ConfigData($this);
		
		$this->language = new TranslationData($this);
		
		$this->brush = new Brush($this);
		$this->undoStore = new UndoStorer($this);
		$this->cloneStore = new CloneStorer($this);
		
		$this->presetManager = new PresetManager($this);
		if(!is_dir($this->getDataFolder())) {
			mkdir($this->getDataFolder());
		}
		if(!is_dir($this->getDataFolder() . "templates/")) {
			mkdir($this->getDataFolder() . "templates/");
		}
		if(!is_dir($this->getDataFolder() . "languages/")) {
			mkdir($this->getDataFolder() . "languages/");
		}
		
		if(!$this->language->collectTranslations()) {
			$this->getLogger()->info(TF::AQUA . "[BlockSniper] No valid language selected, English has been auto-selected.\n" . TF::AQUA . "Please setup a language by using /blocksniper language <lang>.");
		} else {
			$this->getLogger()->info(TF::AQUA . "[BlockSniper] Language selected: " . TF::GREEN . $this->getSettings()->get("Message-Language"));
		}
	}
	
	/**
	 * @return ConfigData
	 */
	public function getSettings(): ConfigData {
		return $this->settings;
	}
	
	public function registerCommands() {
		$blockSniperCommands = [
			"blocksniper" => new BlockSniperCommand($this),
			"brush" => new BrushCommand($this),
			"undo" => new UndoCommand($this),
			"clone" => new CloneCommand($this),
			"paste" => new PasteCommand($this)
		];
		foreach($blockSniperCommands as $name => $class) {
			$this->getServer()->getCommandMap()->register($name, $class);
		}
	}
	
	public function registerListeners() {
		$blockSniperListeners = [
			new BrushListener($this),
			new PresetListener($this),
		];
		foreach($blockSniperListeners as $listener) {
			$this->getServer()->getPluginManager()->registerEvents($listener, $this);
		}
	}
	
	public function onDisable() {
		$this->getLogger()->info(TF::RED . "BlockSniper has been disabled.");
		$this->getUndoStore()->resetUndoStorage();
		$this->getPresetManager()->storePresetsToFile();
	}
	
	/**
	 * @return UndoStorer
	 */
	public function getUndoStore(): UndoStorer {
		return $this->undoStore;
	}
	
	public function getPresetManager(): PresetManager {
		return $this->presetManager;
	}
	
	/**
	 * @return CloneStorer
	 */
	public function getCloneStore(): CloneStorer {
		return $this->cloneStore;
	}
	
	/**
	 * @param string $message
	 *
	 * @return string|null
	 */
	public function getTranslation(string $message): string {
		if($this->language instanceof TranslationData) {
			return $this->language->get($message);
		}
		return null;
	}
}
