<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockSniper\data;

require("plugins/BlockSniper/src/marshal/src/Sandertv/Marshal/Unmarshal.php");
require("plugins/BlockSniper/src/marshal/src/Sandertv/Marshal/Marshal.php");

use BlockHorizons\BlockSniper\Loader;
use pocketmine\item\Item;
use Sandertv\Marshal\DecodeException;
use Sandertv\Marshal\FileNotFoundException;
use Sandertv\Marshal\Marshal;
use Sandertv\Marshal\Unmarshal;

class ConfigData {
	private $filePath = "";

	public $ConfigurationVersion = ""; // Default to an outdated version, so we can properly detect outdated configs.
	public $MessageLanguage = "en";
	/** @var BrushItem */
	public $BrushItem;
	public $MaximumSize = 30;
	public $AsynchronousOperationSize = 15;
	public $MaximumRevertStores = 15;
	public $ResetDecrementBrush = true;
	public $SaveBrushProperties = true;
	public $DropLeafBlowerPlants = true;
	public $OpenGUIAutomatically = true;
	public $MyPlotSupport = false;

	public function __construct(Loader $loader) {
		$this->BrushItem = new BrushItem();
		$this->filePath = $loader->getDataFolder() . "config.yml";

		try {
			Unmarshal::yamlFile($this->filePath, $this);
		} catch(FileNotFoundException $exception) {
			// Make sure to set the right version right off the bat.
			$this->ConfigurationVersion = Loader::CONFIGURATION_VERSION;
			Marshal::yamlFile($this->filePath, $this);
		} catch(\ErrorException $exception) {
			// PM's error handler will create this error exception, causing the DecodeException not to be thrown at all.
			$loader->getLogger()->error("Configuration corrupted. config.yml has been renamed to config_corrupted.yml and a new config.yml has been generated.");
			rename($this->filePath, $loader->getDataFolder() . "config_corrupted.yml");
			// Make sure to set the right version right off the bat.
			$this->ConfigurationVersion = Loader::CONFIGURATION_VERSION;
			Marshal::yamlFile($this->filePath, $this);
		} catch(DecodeException $e) {
		}

		// We can retain backwards compatibility with old configuration most of the times, but the fact that the version
		// was empty means that the configuration was completely unrecoverable. We'll generate a new one.
		if($this->ConfigurationVersion === "") {
			$loader->getLogger()->notice("Outdated configuration. config.yml has been renamed to config_old.yml and a new config.yml has been generated.");
			rename($this->filePath, $loader->getDataFolder() . "config_old.yml");
			// Set the new configuration version so we don't end in an infinite loop.
			$this->ConfigurationVersion = Loader::CONFIGURATION_VERSION;
			Marshal::yamlFile($this->filePath, $this);
		}
	}

	public function __destruct() {
		Marshal::yamlFile($this->filePath, $this);
	}
}

class BrushItem {
	public $ItemID = 396;
	public $ItemData = 0;

	public function parse(): Item {
		return Item::get($this->ItemID, $this->ItemData);
	}
}