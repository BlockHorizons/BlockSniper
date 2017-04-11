<?php

namespace BlockHorizons\BlockSniper;

use BlockHorizons\BlockSniper\brush\BrushManager;
use BlockHorizons\BlockSniper\cloning\CloneStorer;
use BlockHorizons\BlockSniper\commands\BlockSniperCommand;
use BlockHorizons\BlockSniper\commands\BrushCommand;
use BlockHorizons\BlockSniper\commands\cloning\CloneCommand;
use BlockHorizons\BlockSniper\commands\cloning\PasteCommand;
use BlockHorizons\BlockSniper\commands\RedoCommand;
use BlockHorizons\BlockSniper\commands\UndoCommand;
use BlockHorizons\BlockSniper\data\ConfigData;
use BlockHorizons\BlockSniper\data\TranslationData;
use BlockHorizons\BlockSniper\listeners\BrushListener;
use BlockHorizons\BlockSniper\listeners\PresetListener;
use BlockHorizons\BlockSniper\presets\PresetManager;
use BlockHorizons\BlockSniper\tasks\UndoDiminishTask;
use BlockHorizons\BlockSniper\undo\UndoStorer;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\TextFormat as TF;

class Loader extends PluginBase {
	
	const VERSION = "1.3.4";
	const API_TARGET = "2.0.0 - 3.0.0-ALPHA4";
	const CONFIGURATION_VERSION = "1.0.1";
	
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
	private $undoStore;
	private $cloneStore;
	private $settings;
	private $brushManager;
	private $presetManager;
	
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

		$this->presetManager = new PresetManager($this);
		$this->brushManager = new BrushManager($this);

		$this->undoStore = new UndoStorer($this);
		$this->cloneStore = new CloneStorer($this);

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
			"redo" => new RedoCommand($this),
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
		$this->getPresetManager()->storePresetsToFile();
		$this->getBrushManager()->storeBrushesToFile();
		$this->getSettings()->save();

		$this->getLogger()->info(TF::RED . "BlockSniper has been disabled.");
	}
	
	/**
	 * @return UndoStorer
	 */
	public function getUndoStore(): UndoStorer {
		return $this->undoStore;
	}
	
	/**
	 * @return PresetManager
	 */
	public function getPresetManager(): PresetManager {
		return $this->presetManager;
	}

	/**
	 * @return BrushManager
	 */
	public function getBrushManager(): BrushManager {
		return $this->brushManager;
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
