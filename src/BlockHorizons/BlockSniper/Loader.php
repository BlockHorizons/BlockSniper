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
use BlockHorizons\BlockSniper\git\GitRepository;
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

	const VERSION = "2.2.0";
	const API_TARGET = "3.0.0-ALPHA7 - 3.0.0-ALPHA9";
	const CONFIGURATION_VERSION = "2.5.0";

	/** @var string[] */
	private static $availableLanguages = [
		"en",
		"nl",
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

	public function onEnable(): void {
		$this->reloadAll();

		$this->registerCommands();
		$this->registerListeners();

		$this->getServer()->getScheduler()->scheduleRepeatingTask(new UndoDiminishTask($this), 400);
		$this->getServer()->getScheduler()->scheduleRepeatingTask(new RedoDiminishTask($this), 400);
	}

	private function reloadAll(): void {
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

		$this->saveResource("settings.yml");
		$this->settings = new ConfigData($this);
		$this->language = new TranslationData($this);
		$this->presetManager = new PresetManager($this);

		if(!$this->language->collectTranslations()) {
			$this->getLogger()->info(TF::AQUA . (new Translation(Translation::LOG_LANGUAGE_AUTO_SELECTED))->getMessage());
			$this->getLogger()->info(TF::AQUA . (new Translation(Translation::LOG_LANGUAGE_USAGE))->getMessage());
		} else {
			$this->getLogger()->info(TF::AQUA . (new Translation(Translation::LOG_LANGUAGE_SELECTED))->getMessage() . TF::GREEN . $this->getSettings()->getLanguage());
		}
		new GitRepository($this);

		ShapeRegistration::init();
		TypeRegistration::init();

		if($this->getSettings()->hasMyPlotSupport()) {
			$this->myPlot = $this->getServer()->getPluginManager()->getPlugin("MyPlot");
		}
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
		$this->getServer()->getCommandMap()->registerAll("blocksniper", [
			new BlockSniperCommand($this),
			new BrushCommand($this),
			new UndoCommand($this),
			new RedoCommand($this),
			new CloneCommand($this),
			new PasteCommand($this)
		]);
	}

	public function registerListeners(): void {
		$blockSniperListeners = [
			new BrushListener($this),
			new UserInterfaceListener($this),
			$this->sessionManager = new SessionManager($this)
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

	public function getCelestialAngle(int $x = 14000): float {
		$x = 12000;
		$x /= 24000; // Divide by total time to get a float 0.0 - 1.0
					                      // 0.5
		$x += ($x < 0.25 ? 0.75 : -0.25); // 0.5 - 0.25 = 0.25.                         Range between: 0 - 0.99, 0.99 for Noon,
		$cos = cos($x * M_PI);        // 0.25 * pi = 0.7854, cos(0.7854) = 0.7071
		$cos += 1;                        // 0.7071 + 1 = 1.7071
		$cos /= 2;                        // 1.7071 / 2 = 0.8536
		$cos = 1 - $cos;                  // 1 - 0.8536 = 0.1464
		$cos -= $x;                       // 0.1464 - 0.2500 = -0.1036
		$cos /= 3;                        // -0.1036 / 3 = -0.0345333333
		$celestialAngle = $x + $cos;      // 0.25 + -0.0345333333 = 0.2155
		var_dump($celestialAngle);
		var_dump($x + (((1 - ((cos($x * M_PI) + 1) / 2)) - $x) / 3));
	}
}
