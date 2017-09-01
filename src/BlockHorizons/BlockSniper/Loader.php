<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockSniper;

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
use BlockHorizons\BlockSniper\git\GitRepository;
use BlockHorizons\BlockSniper\listeners\BrushListener;
use BlockHorizons\BlockSniper\listeners\UserInterfaceListener;
use BlockHorizons\BlockSniper\presets\PresetManager;
use BlockHorizons\BlockSniper\sessions\SessionManager;
use BlockHorizons\BlockSniper\tasks\UndoDiminishTask;
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
	];
	/** @var TranslationData */
	public $language = null;
	/** @var PresetManager */
	private $presetManager = null;
	/** @var ConfigData */
	protected $settings = null;

	/**
	 * @return array
	 */
	public static function getAvailableLanguages(): array {
		return self::$availableLanguages;
	}

	public function onEnable(): void {
		$this->reloadAll();

		$this->registerCommands();
		$this->registerListeners();

		$this->getServer()->getScheduler()->scheduleRepeatingTask(new UndoDiminishTask($this), 400);
		CommandOverloads::initialize();
	}

	private function reloadAll(): void {
		$this->saveResource("settings.yml");
		$this->settings = new ConfigData($this);
		$this->language = new TranslationData($this);
		$this->presetManager = new PresetManager($this);

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

		if(!$this->language->collectTranslations()) {
			$this->getLogger()->info(TF::AQUA . (new Translation(Translation::LOG_LANGUAGE_AUTO_SELECTED))->getMessage());
			$this->getLogger()->info(TF::AQUA . (new Translation(Translation::LOG_LANGUAGE_USAGE))->getMessage());
		} else {
			$this->getLogger()->info(TF::AQUA . (new Translation(Translation::LOG_LANGUAGE_SELECTED))->getMessage() . TF::GREEN . $this->getSettings()->getLanguage());
		}
		new GitRepository($this);
	}

	public function reload(): void {
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

	public function registerCommands(): void {
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

	public function registerListeners(): void {
		$blockSniperListeners = [
			new BrushListener($this),
			new UserInterfaceListener($this),
			new SessionManager($this)
		];
		foreach($blockSniperListeners as $listener) {
			$this->getServer()->getPluginManager()->registerEvents($listener, $this);
		}
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
	 * @return TranslationData
	 */
	public function getTranslationData(): TranslationData {
		return $this->language;
	}
}
