<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper;

use BlockHorizons\BlockSniper\brush\registration\ShapeRegistration;
use BlockHorizons\BlockSniper\brush\registration\TypeRegistration;
use BlockHorizons\BlockSniper\changelog\ChangelogTask;
use BlockHorizons\BlockSniper\command\BlockSniperCommand;
use BlockHorizons\BlockSniper\command\BrushCommand;
use BlockHorizons\BlockSniper\command\cloning\CloneCommand;
use BlockHorizons\BlockSniper\command\cloning\PasteCommand;
use BlockHorizons\BlockSniper\command\RedoCommand;
use BlockHorizons\BlockSniper\command\UndoCommand;
use BlockHorizons\BlockSniper\command\DeselectCommand;
use BlockHorizons\BlockSniper\data\ConfigData;
use BlockHorizons\BlockSniper\data\Translation;
use BlockHorizons\BlockSniper\data\TranslationData;
use BlockHorizons\BlockSniper\listener\BrushListener;
use BlockHorizons\BlockSniper\session\SessionManager;
use BlockHorizons\BlockSniper\task\UpdateNotifyTask;
use MyPlot\MyPlot;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\TextFormat as TF;
use function is_dir;
use function mkdir;

class Loader extends PluginBase{

	public const VERSION = "4.0.0";
	public const CONFIGURATION_VERSION = "4.3.1";
	public const API_TARGET = "4.0.0";

	private const AUTOLOAD_LIBRARIES = [
		"marshal",
		"schematic"
	];

	/**
	 * @var string[]
	 * @phpstan-var list<string>
	 */
	private static $availableLanguages = [
		"en",
		"nl",
		"fr",
		"ko",
		"ja",
		"zh-hans",
		"zh-hant",
		"es"
	];
	/** @var TranslationData */
	private $language = null;
	/** @var ConfigData */
	public $config = null;

	/** @var null|MyPlot */
	private $myPlot = null;

	/** @var BrushListener */
	private $listener;

	/**
	 * @return string[]
	 * @phpstan-return list<string>
	 */
	public static function getAvailableLanguages() : array{
		return self::$availableLanguages;
	}

	public function onLoad() : void{
		foreach(self::AUTOLOAD_LIBRARIES as $name){
			$this->getServer()->getLoader()->addPath("", $this->getFile() . "src/$name/src");
		}

		$this->getServer()->getAsyncPool()->submitTask(new UpdateNotifyTask());
		$this->getServer()->getAsyncPool()->submitTask(new ChangelogTask());
	}

	public function onEnable() : void{
		$this->load();

		$this->registerCommands();
		$this->registerListeners();
	}

	public function onDisable() : void{
		SessionManager::close();
		$this->config->close();
		$this->listener->saveBrushes();
	}

	public function reload() : void{
		$this->onDisable();
		$this->load();
	}

	private function load() : void{
		$this->initializeDirectories();

		$this->config = new ConfigData($this);
		$this->language = new TranslationData($this);

		if(!$this->language->collectTranslations()){
			$this->getLogger()->info(Translation::get(Translation::LOG_LANGUAGE_AUTO_SELECTED));
			$this->getLogger()->info(Translation::get(Translation::LOG_LANGUAGE_USAGE));
		}else{
			$this->getLogger()->info(Translation::get(Translation::LOG_LANGUAGE_SELECTED) . TF::GREEN . $this->config->messageLanguage);
		}

		ShapeRegistration::init();
		TypeRegistration::init();

		if($this->config->myPlotSupport){
			$this->myPlot = $this->getServer()->getPluginManager()->getPlugin("MyPlot");
		}
	}

	public function initializeDirectories() : void{
		if(!is_dir($this->getDataFolder() . "schematics/")){
			mkdir($this->getDataFolder() . "schematics/");
		}
		if(!is_dir($this->getDataFolder() . "languages/")){
			mkdir($this->getDataFolder() . "languages/");
		}
		if(!is_dir($this->getDataFolder() . "sessions/")){
			mkdir($this->getDataFolder() . "sessions/");
		}
	}

	/**
	 * @return MyPlot|null
	 */
	public function getMyPlot() : ?MyPlot{
		return $this->myPlot;
	}

	/**
	 * @return bool
	 */
	public function isMyPlotAvailable() : bool{
		return $this->myPlot !== null;
	}

	private function registerCommands() : void{
		$this->getServer()->getCommandMap()->registerAll("blocksniper", [
				new BlockSniperCommand($this),
				new BrushCommand($this),
				new UndoCommand($this),
				new RedoCommand($this),
				new CloneCommand($this),
				new DeselectCommand($this),
				new PasteCommand($this)
			]
		);
	}

	private function registerListeners() : void{
		$listeners = [
			$this->listener = new BrushListener($this),
		];
		foreach($listeners as $listener){
			$this->getServer()->getPluginManager()->registerEvents($listener, $this);
		}
	}
}
