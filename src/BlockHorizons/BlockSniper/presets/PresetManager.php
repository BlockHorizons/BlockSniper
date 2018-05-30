<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockSniper\presets;

use BlockHorizons\BlockSniper\data\Translation;
use BlockHorizons\BlockSniper\Loader;
use pocketmine\utils\TextFormat as TF;

class PresetManager {

	/** @var Loader */
	private $loader = null;
	/** @var array */
	private $preset = [];

	public function __construct(Loader $loader) {
		$this->loader = $loader;

		if(is_file($loader->getDataFolder() . "presets.yml")) {
            $data = yaml_parse_file($loader->getDataFolder() . "presets.yml");
            foreach ($data as $name => $data) {
				$this->addPreset(unserialize($data, ["allowed_classes" => [Preset::class]]));
                $loader->getLogger()->debug(Translation::get(Translation::LOG_PRESETS_LOADED, [$name]));
			}
            $loader->getLogger()->debug(Translation::get(Translation::LOG_PRESETS_ALL_LOADED));
		}
	}

	/**
	 * @param Preset $preset
	 */
	public function addPreset(Preset $preset): void {
		$this->preset[$preset->name] = $preset;
	}

	/**
	 * @param string $name
	 *
	 * @return bool
	 */
	public function isPreset(string $name): bool {
		return isset($this->preset[$name]);
	}

	/**
	 * @param string $name
	 *
	 * @return Preset
	 */
	public function getPreset(string $name): Preset {
		return $this->preset[$name];
	}

	/**
	 * @param string $name
	 */
	public function deletePreset(string $name): void {
		unset($this->preset[$name]);
	}

	public function storePresetsToFile(): void {
		$data = [];
		if(!empty($this->preset)) {
			foreach($this->preset as $name => $preset) {
				if($preset instanceof Preset) {
					$data[$name] = serialize($preset);
				}
			}
		}
		yaml_emit_file($this->getLoader()->getDataFolder() . "presets.yml", $data);
	}

	/**
	 * @return Loader
	 */
	public function getLoader(): Loader {
		return $this->loader;
	}

	/**
	 * @return array
	 */
	public function getAllPresets(): array {
		$presets = [];
		foreach($this->preset as $name => $preset) {
			$presets[] = $name;
		}
		return $presets;
	}
}
