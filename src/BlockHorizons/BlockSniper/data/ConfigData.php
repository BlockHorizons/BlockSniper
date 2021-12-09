<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\data;

use BlockHorizons\BlockSniper\Loader;
use ErrorException;
use Sandertv\Marshal\DecodeException;
use Sandertv\Marshal\FileNotFoundException;
use Sandertv\Marshal\Marshal;
use Sandertv\Marshal\Unmarshal;
use function rename;

class ConfigData{
	private string $filePath = "";

	/**
	 * @var string
	 * @marshal Configuration Version
	 */
	public $configurationVersion = ""; // Default to an empty version, so we can properly detect outdated configs.
	/**
	 * @var string
	 * @marshal Message Language
	 */
	public $messageLanguage = "en";
	/**
	 * @var BrushItem
	 * @marshal Brush Item
	 */
	public $brushItem;
	/**
	 * @var BrushItem
	 * @marshal Selection Item
	 */
	public $selectionItem;
	/**
	 * @var int
	 * @marshal Maximum Size
	 */
	public $maxSize = 30;
	/**
	 * @var int
	 * @marshal Asynchronous Operation Size
	 */
	public $asyncOperationSize = 15;
	/**
	 * @var int
	 * @marshal Maximum Revert Stores
	 */
	public $maxRevertStores = 15;
	/**
	 * @var bool
	 * @marshal Reset Decrement Brush
	 */
	public $resetDecrementBrush = true;
	/**
	 * @var bool
	 * @marshal Save Brush Properties
	 */
	public $saveBrushProperties = true;
	/**
	 * @var int
	 * @marshal Session Timeout Time in minutes
	 */
	public $sessionTimeoutTime = 5;
	/**
	 * @var float
	 * @marshal Brush cooldown in seconds
	 */
	public $cooldownSeconds = 0.0;
	/**
	 * @var bool
	 * @marshal Open GUI Automatically
	 */
	public $openGuiAutomatically = true;
	/**
	 * @var bool
	 * @marshal MyPlot Support
	 */
	public $myPlotSupport = false;

	public function __construct(Loader $loader){
		$this->brushItem = new BrushItem();
		$this->selectionItem = new BrushItem();
		$this->selectionItem->item = "glowstone_dust";

		$this->filePath = $loader->getDataFolder() . "config.yml";

		try{
			Unmarshal::yamlFile($this->filePath, $this);
		}catch(FileNotFoundException $exception){
			// Make sure to set the right version right off the bat.
			$this->configurationVersion = Loader::CONFIGURATION_VERSION;
			Marshal::yamlFile($this->filePath, $this);
		}catch(ErrorException $exception){
			// PM's error handler will create this error exception, causing the DecodeException not to be thrown at all.
			$loader->getLogger()->error("Configuration corrupted. config.yml has been renamed to config_corrupted.yml and a new config.yml has been generated.");
			rename($this->filePath, $loader->getDataFolder() . "config_corrupted.yml");
			// Make sure to set the right version right off the bat.
			$this->configurationVersion = Loader::CONFIGURATION_VERSION;
			Marshal::yamlFile($this->filePath, $this);
		}catch(DecodeException $e){
			// Never hit because of the block above.
		}

		// We can retain backwards compatibility with old configuration most of the times, but the fact that the version
		// was empty means that the configuration was completely unrecoverable. We'll generate a new one.
		if($this->configurationVersion === ""){
			$loader->getLogger()->notice("Outdated configuration. config.yml has been renamed to config_old.yml and a new config.yml has been generated.");
			rename($this->filePath, $loader->getDataFolder() . "config_old.yml");
			// Set the new configuration version so we don't end in an infinite loop.
			$this->configurationVersion = Loader::CONFIGURATION_VERSION;
			Marshal::yamlFile($this->filePath, $this);
		}
	}

	public function close() : void{
		Marshal::yamlFile($this->filePath, $this);
	}
}