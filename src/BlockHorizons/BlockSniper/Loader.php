<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockSniper;

use BlockHorizons\BlockSniper\brush\registration\ShapeRegistration;
use BlockHorizons\BlockSniper\brush\registration\TypeRegistration;
use BlockHorizons\BlockSniper\commands\BlockSniperCommand;
use BlockHorizons\BlockSniper\commands\BrushCommand;
use BlockHorizons\BlockSniper\commands\cloning\CloneCommand;
use BlockHorizons\BlockSniper\commands\cloning\PasteCommand;
use BlockHorizons\BlockSniper\commands\RedoCommand;
use BlockHorizons\BlockSniper\commands\UndoCommand;
use BlockHorizons\BlockSniper\data\ConfigData;
use BlockHorizons\BlockSniper\data\Translation;
use BlockHorizons\BlockSniper\data\TranslationData;
use BlockHorizons\BlockSniper\listeners\BrushListener;
use BlockHorizons\BlockSniper\listeners\UserInterfaceListener;
use BlockHorizons\BlockSniper\presets\PresetManager;
use BlockHorizons\BlockSniper\sessions\SessionManager;
use BlockHorizons\BlockSniper\tasks\RedoDiminishTask;
use BlockHorizons\BlockSniper\tasks\UndoDiminishTask;
use MyPlot\MyPlot;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\TextFormat as TF;

class Loader extends PluginBase {

	const VERSION = "2.3.1";
	const API_TARGET = "3.0.0-ALPHA10 - 3.0.0-ALPHA12";
	const CONFIGURATION_VERSION = "2.5.0";

	/** @var string[] */
	private static $availableLanguages = [
		"en",
		"nl",
		"fr",
		"ko",
		"ja",
		"zh-hans",
		"zh-hant"
	];
	/** @var TranslationData */
	private $language = null;
	/** @var PresetManager */
	private $presetManager = null;
	/** @var ConfigData */
	private $settings = null;
	/** @var SessionManager */
	private $sessionManager = null;

	/** @var null|MyPlot */
	private $myPlot = null;

	/**
	 * @return string[]
	 */
	public static function getAvailableLanguages(): array {
		return self::$availableLanguages;
	}

	public function reload(): void {
		$this->onDisable();
		$this->reloadAll();
	}

	public function onDisable(): void {
		$this->getPresetManager()->storePresetsToFile();
		$this->getSettings()->save();
	}

	/**
	 * @return PresetManager
	 */
	public function getPresetManager(): PresetManager {
		return $this->presetManager;
	}

	/**
	 * @return ConfigData
	 */
	public function getSettings(): ConfigData {
		return $this->settings;
	}

	private function reloadAll(): void {
		$this->initializeDirectories();

		$this->saveResource("settings.yml");
		$this->settings = new ConfigData($this);
		$this->language = new TranslationData($this);
		$this->presetManager = new PresetManager($this);

		if(!$this->language->collectTranslations()) {
			$this->getLogger()->info(Translation::get(Translation::LOG_LANGUAGE_AUTO_SELECTED));
			$this->getLogger()->info(Translation::get(Translation::LOG_LANGUAGE_USAGE));
		} else {
			$this->getLogger()->info(Translation::get(Translation::LOG_LANGUAGE_SELECTED) . TF::GREEN . $this->getSettings()->getLanguage());
		}

		ShapeRegistration::init();
		TypeRegistration::init();

		if($this->getSettings()->hasMyPlotSupport()) {
			$this->myPlot = $this->getServer()->getPluginManager()->getPlugin("MyPlot");
		}
	}

	public function initializeDirectories(): void {
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
		if(!is_dir($this->getDataFolder() . "sessions/")) {
			mkdir($this->getDataFolder() . "sessions/");
			file_put_contents($this->getDataFolder() . "sessions/players.json", "");
		}
	}

	/**
	 * @return TranslationData
	 */
	public function getTranslationData(): TranslationData {
		return $this->language;
	}

	/**
	 * @return SessionManager
	 */
	public function getSessionManager(): SessionManager {
		return $this->sessionManager;
	}

	/**
	 * @return MyPlot|null
	 */
	public function getMyPlot(): ?MyPlot {
		return $this->myPlot;
	}

	/**
	 * @return bool
	 */
	public function isMyPlotAvailable(): bool {
		return $this->myPlot !== null;
	}

	public function onEnable(): void {
		$this->reloadAll();

		$this->registerCommands();
		$this->registerListeners();

		$this->getScheduler()->scheduleRepeatingTask(new UndoDiminishTask($this), 400);
		$this->getScheduler()->scheduleRepeatingTask(new RedoDiminishTask($this), 400);
	}

	private function registerCommands(): void {
		$this->getServer()->getCommandMap()->registerAll("blocksniper", [
			new BlockSniperCommand($this),
			new BrushCommand($this),
			new UndoCommand($this),
			new RedoCommand($this),
			new CloneCommand($this),
			new PasteCommand($this)
		]);
	}

	private function registerListeners(): void {
		$blockSniperListeners = [
			new BrushListener($this),
			new UserInterfaceListener($this),
			$this->sessionManager = new SessionManager($this)
		];
		foreach($blockSniperListeners as $listener) {
			$this->getServer()->getPluginManager()->registerEvents($listener, $this);
		}
	}
}
