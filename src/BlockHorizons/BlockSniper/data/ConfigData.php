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

	public $ConfigurationVersion = Loader::CONFIGURATION_VERSION;
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
			Unmarshal::YamlFile($this->filePath, $this);
		} catch(FileNotFoundException $exception) {
			Marshal::YamlFile($this->filePath, $this);
		} catch(DecodeException $exception) {
			$loader->getLogger()->error("Configuration corrupted. config.yml has been renamed to config_corrupted.yml and a new config.yml has been generated.");
			rename($this->filePath, $loader->getDataFolder() . "config_corrupted.yml");
			Marshal::YamlFile($this->filePath, $this);
		}
	}

	public function __destruct() {
		Marshal::YamlFile($this->filePath, $this);
	}
}

class BrushItem {
	public $ItemID = 396;
	public $ItemData = 0;

	public function parse(): Item {
		return Item::get($this->ItemID, $this->ItemData);
	}
}