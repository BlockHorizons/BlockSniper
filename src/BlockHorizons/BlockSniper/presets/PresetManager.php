<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\presets;

use BlockHorizons\BlockSniper\data\Translation;
use BlockHorizons\BlockSniper\Loader;
use Sandertv\Marshal\DecodeException;
use Sandertv\Marshal\FileNotFoundException;
use Sandertv\Marshal\Marshal;
use Sandertv\Marshal\Unmarshal;

class PresetManager{

	/** @var Loader */
	private $loader = null;
	/** @var Preset[] */
	private $presets = [];

	public function __construct(Loader $loader){
		$this->loader = $loader;

		foreach(scandir($loader->getDataFolder() . "presets") as $fileName){
			if($fileName === "." || $fileName === ".."){
				continue;
			}
			if(!is_file($loader->getDataFolder() . "presets/" . $fileName)){
				continue;
			}
			$preset = new Preset("");
			try{
				Unmarshal::jsonFile($loader->getDataFolder() . "presets/" . $fileName, $preset);
			}catch(DecodeException $exception){
				$loader->getLogger()->logException($exception);
			}catch(FileNotFoundException $exception){
				$loader->getLogger()->logException($exception);
			}
			$this->presets[] = $preset;
			$loader->getLogger()->debug(Translation::get(Translation::LOG_PRESETS_LOADED, [$preset->name]) . " (" . json_encode($preset) . ")");
		}
		$loader->getLogger()->debug(Translation::get(Translation::LOG_PRESETS_ALL_LOADED));
	}

	/**
	 * @param Preset $preset
	 */
	public function addPreset(Preset $preset) : void{
		$this->presets[] = $preset;
	}

	/**
	 * @param string $name
	 *
	 * @return bool
	 */
	public function isPreset(string $name) : bool{
		foreach($this->presets as $preset){
			if($name === $preset->name){
				return true;
			}
		}

		return false;
	}

	/**
	 * @param int $index
	 *
	 * @return Preset
	 */
	public function getPreset(int $index) : Preset{
		return $this->presets[$index];
	}

	/**
	 * @param int $offset
	 */
	public function deletePreset(int $offset) : void{
		unset($this->presets[$offset]);
	}

	public function storePresetsToFile() : void{
		foreach($this->presets as $index => $preset){
			Marshal::jsonFile($this->loader->getDataFolder() . "presets/" . $preset->name . ".json", $preset);
		}
	}

	/**
	 * @return Loader
	 */
	public function getLoader() : Loader{
		return $this->loader;
	}

	/**
	 * @return Preset[]
	 */
	public function getAllPresets() : array{
		return $this->presets;
	}
}
