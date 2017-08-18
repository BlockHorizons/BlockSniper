<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockSniper;

use BlockHorizons\BlockSniper\brush\BrushManager;
use BlockHorizons\BlockSniper\cloning\CloneStorer;
use BlockHorizons\BlockSniper\commands\BlockSniperCommand;
use BlockHorizons\BlockSniper\commands\BrushCommand;
use BlockHorizons\BlockSniper\commands\cloning\CloneCommand;
use BlockHorizons\BlockSniper\commands\cloning\PasteCommand;
use BlockHorizons\BlockSniper\commands\CommandOverloads;
use BlockHorizons\BlockSniper\commands\RedoCommand;
use BlockHorizons\BlockSniper\commands\UndoCommand;
use BlockHorizons\BlockSniper\data\ConfigData;
use BlockHorizons\BlockSniper\data\Translation;
use BlockHorizons\BlockSniper\data\TranslationData;
use BlockHorizons\BlockSniper\listeners\BrushListener;
use BlockHorizons\BlockSniper\listeners\UserInterfaceListener;
use BlockHorizons\BlockSniper\presets\PresetManager;
use BlockHorizons\BlockSniper\tasks\UndoDiminishTask;
use BlockHorizons\BlockSniper\undo\RevertStorer;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\TextFormat as TF;

class Loader extends PluginBase {

	const VERSION = "2.0.0";
	const API_TARGET = "3.0.0-ALPHA7";
	const CONFIGURATION_VERSION = "2.4.0";

	/** @var string[] */
	private static $availableLanguages = [
		"en",
		"nl",
		"de",
		"fr",
		"fa",
		"ru",
		"zh_tw"
	];
	/** @var TranslationData */
	public $language = null;

	/** @var RevertStorer */
	private $revertStorer = null;
	/** @var CloneStorer */
	private $cloneStorer = null;
	/** @var ConfigData */
	private $settings = null;
	/** @var BrushManager */
	private $brushManager = null;
	/** @var PresetManager */
	private $presetManager = null;

	/**
	 * @return array
	 */
	public static function getAvailableLanguages(): array {
		return self::$availableLanguages;
	}

	public function onEnable() {
		$this->reloadAll();

		$this->registerCommands();
		$this->registerListeners();

		$this->getServer()->getScheduler()->scheduleRepeatingTask(new UndoDiminishTask($this), 400);
		CommandOverloads::initialize();
	}

	private function reloadAll() {
		$this->saveResource("settings.yml");
		$this->settings = new ConfigData($this);
		$this->language = new TranslationData($this);

		$this->presetManager = new PresetManager($this);
		$this->brushManager = new BrushManager($this);

		$this->revertStorer = new RevertStorer($this);
		$this->cloneStorer = new CloneStorer($this);

		if(!is_dir($this->getDataFolder())) {
			mkdir($this->getDataFolder());
		}
		if(!is_dir($this->getDataFolder() . "templates/")) {
			mkdir($this->getDataFolder() . "templates/");
		}
		if(!is_dir($this->getDataFolder() . "schematics/")) {
			mkdir($this->getDataFolder() . "schematics/");
		}
		if(!is_dir($this->getDataFolder() . "languages/")) {
			mkdir($this->getDataFolder() . "languages/");
		}

		if(!$this->language->collectTranslations()) {
			$this->getLogger()->info(TF::AQUA . (new Translation(Translation::LOG_LANGUAGE_AUTO_SELECTED))->getMessage());
			$this->getLogger()->info(TF::AQUA . (new Translation(Translation::LOG_LANGUAGE_USAGE))->getMessage());
		} else {
			$this->getLogger()->info(TF::AQUA . (new Translation(Translation::LOG_LANGUAGE_SELECTED))->getMessage() . TF::GREEN . $this->getSettings()->getLanguage());
		}
	}

	public function reload() {
		$this->getLogger()->info(TF::AQUA . (new Translation(Translation::LOG_RELOAD_START))->getMessage());
		$this->onDisable();
		$this->reloadAll();
		$this->getLogger()->info(TF::AQUA . (new Translation(Translation::LOG_RELOAD_FINISH))->getMessage());
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
			new UserInterfaceListener($this)
		];
		foreach($blockSniperListeners as $listener) {
			$this->getServer()->getPluginManager()->registerEvents($listener, $this);
		}
	}

	public function onDisable() {
		$this->getPresetManager()->storePresetsToFile();
		$this->getBrushManager()->storeBrushesToFile();
		$this->getSettings()->save();
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
	public function getCloneStorer(): CloneStorer {
		return $this->cloneStorer;
	}

	/**
	 * @return TranslationData
	 */
	public function getTranslationData(): TranslationData {
		return $this->language;
	}

	/**
	 * @return RevertStorer
	 */
	public function getRevertStorer(): RevertStorer {
		return $this->revertStorer;
	}
}
