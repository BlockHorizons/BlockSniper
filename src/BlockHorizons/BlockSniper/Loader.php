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
use BlockHorizons\BlockSniper\presets\PresetManager;
use BlockHorizons\BlockSniper\sessions\SessionManager;
use BlockHorizons\BlockSniper\tasks\RedoDiminishTask;
use BlockHorizons\BlockSniper\tasks\UndoDiminishTask;
use MyPlot\MyPlot;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\TextFormat as TF;

class Loader extends PluginBase {

	const VERSION = "3.0.0";
	const API_TARGET = "3.0.0";
	const CONFIGURATION_VERSION = "3.0.0";

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
	public $config = null;
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

	public function onEnable(): void {
		$this->load();

		$this->registerCommands();
		$this->registerListeners();

		$this->getScheduler()->scheduleRepeatingTask(new UndoDiminishTask($this), 400);
		$this->getScheduler()->scheduleRepeatingTask(new RedoDiminishTask($this), 400);
	}

	public function onDisable(): void {
		$this->getPresetManager()->storePresetsToFile();
		$this->sessionManager->close();
	}

	public function reload(): void {
		$this->onDisable();
		$this->config->__destruct();
		$this->load();
	}

	/**
	 * @return PresetManager
	 */
	public function getPresetManager(): PresetManager {
		return $this->presetManager;
	}

	private function load(): void {
		$this->initializeDirectories();

		$this->config = new ConfigData($this);
		$this->language = new TranslationData($this);
		$this->presetManager = new PresetManager($this);

		if(!$this->language->collectTranslations()) {
			$this->getLogger()->info(Translation::get(Translation::LOG_LANGUAGE_AUTO_SELECTED));
			$this->getLogger()->info(Translation::get(Translation::LOG_LANGUAGE_USAGE));
		} else {
			$this->getLogger()->info(Translation::get(Translation::LOG_LANGUAGE_SELECTED) . TF::GREEN . $this->config->MessageLanguage);
		}

		ShapeRegistration::init();
		TypeRegistration::init();

		if($this->config->MyPlotSupport) {
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
		}
		if(!is_dir($this->getDataFolder() . "presets/")) {
			mkdir($this->getDataFolder() . "presets/");
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
			$this->sessionManager = new SessionManager($this)
		];
		foreach($blockSniperListeners as $listener) {
			$this->getServer()->getPluginManager()->registerEvents($listener, $this);
		}
	}
}
