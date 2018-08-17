<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockSniper\presets;

require_once("plugins/BlockSniper/src/marshal/src/Sandertv/Marshal/Unmarshal.php");
require_once("plugins/BlockSniper/src/marshal/src/Sandertv/Marshal/DecodeException.php");

use BlockHorizons\BlockSniper\data\Translation;
use BlockHorizons\BlockSniper\Loader;
use Sandertv\Marshal\DecodeException;
use Sandertv\Marshal\Unmarshal;

class PresetManager {

	/** @var Loader */
	private $loader = null;
	/** @var Preset[] */
	private $presets = [];

	public function __construct(Loader $loader) {
		$this->loader = $loader;

		if(is_file($loader->getDataFolder() . "presets.yml")) {
			$data = json_decode(file_get_contents($loader->getDataFolder() . "presets.json"));
			foreach($data as $name => $datum) {
				$preset = new Preset($name);
				try {
					Unmarshal::json($datum, $preset);
				} catch(DecodeException $exception) {
					continue;
				}

				$this->addPreset($preset);
				$loader->getLogger()->debug(Translation::get(Translation::LOG_PRESETS_LOADED, [$name]));
			}
			$loader->getLogger()->debug(Translation::get(Translation::LOG_PRESETS_ALL_LOADED));
		}
	}

	/**
	 * @param Preset $preset
	 */
	public function addPreset(Preset $preset): void {
		$this->presets[] = $preset;
	}

	/**
	 * @param string $name
	 *
	 * @return bool
	 */
	public function isPreset(string $name): bool {
		foreach($this->presets as $preset) {
			if($name === $preset->name) {
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
	public function getPreset(int $index): Preset {
		return $this->presets[$index];
	}

	/**
	 * @param int $offset
	 */
	public function deletePreset(int $offset): void {
		unset($this->presets[$offset]);
	}

	public function storePresetsToFile(): void {
		$data = [];
		foreach($this->presets as $index => $preset) {
			$data[$preset->name] = json_encode($preset);
		}
		file_put_contents($this->getLoader()->getDataFolder() . "presets.yml", json_encode($data));
	}

	/**
	 * @return Loader
	 */
	public function getLoader(): Loader {
		return $this->loader;
	}

	/**
	 * @return Preset[]
	 */
	public function getAllPresets(): array {
		$presets = [];
		foreach($this->presets as $name => $preset) {
			$presets[] = $name;
		}
		return $presets;
	}
}
