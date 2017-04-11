<?php

namespace BlockHorizons\BlockSniper\presets;

use BlockHorizons\BlockSniper\Loader;
use pocketmine\Player;
use pocketmine\utils\TextFormat as TF;

class PresetManager {
	
	public $presetCreation = [];
	private $loader;
	private $data;
	private $preset;
	
	public function __construct(Loader $loader) {
		$this->loader = $loader;
		
		if(is_file($loader->getDataFolder() . "presets.yml")) {
			$this->data = yaml_parse_file($loader->getDataFolder() . "presets.yml");
			foreach($this->data as $name => $data) {
				$this->addPreset($name);
				$loader->getLogger()->debug(TF::GREEN . "Preset " . $name . " has been loaded.");
			}
			unlink($loader->getDataFolder() . "presets.yml");
			$loader->getLogger()->info(TF::GREEN . "All presets have been loaded.");
		}
	}
	
	/**
	 * @param string $name
	 */
	public function addPreset(string $name) {
		$this->preset[$name] = new Preset(
			$this->data[$name]["name"],
			$this->data[$name]["shape"],
			$this->data[$name]["type"],
			$this->data[$name]["decrement"],
			$this->data[$name]["perfect"],
			$this->data[$name]["size"],
			$this->data[$name]["hollow"],
			$this->data[$name]["blocks"],
			$this->data[$name]["obsolete"],
			$this->data[$name]["height"],
			$this->data[$name]["biome"]);
		unset($this->data[$name]);
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
	 * @param Player $player
	 *
	 * @return bool
	 */
	public function isCreatingAPreset(Player $player): bool {
		return isset($this->presetCreation[$player->getId()]);
	}
	
	/**
	 * @param Player $player
	 *
	 * @return int
	 */
	public function getCurrentPresetCreationProgress(Player $player): int {
		return count($this->presetCreation[$player->getId()]);
	}
	
	/**
	 * @param Player $player
	 * @param string $name
	 */
	public function parsePresetCreationInfo(Player $player, string $name) {
		foreach($this->presetCreation[$player->getId()] as $key => $value) {
			$this->data[$name][$key] = $value;
		}
		$this->addPreset($name);
		unset($this->presetCreation[$player->getId()]);
	}
	
	/**
	 * @param Player $player
	 * @param string $key
	 * @param        $value
	 */
	public function addToCreationData(Player $player, string $key, $value) {
		$this->presetCreation[$player->getId()][$key] = $value;
	}
	
	/**
	 * @param Player $player
	 * @param string $key
	 *
	 * @return mixed
	 */
	public function getCreationData(Player $player, string $key = null) {
		if(isset($key)) {
			return $this->presetCreation[$player->getId()][$key];
		}
		return $this->presetCreation[$player->getId()];
	}
	
	/**
	 * @param Player $player
	 */
	public function cancelPresetCreationProcess(Player $player) {
		unset($this->presetCreation[$player->getId()]);
	}
	
	/**
	 * @param string $name
	 */
	public function deletePreset(string $name) {
		unset($this->preset[$name]);
	}
	
	public function storePresetsToFile() {
		$data = [];
		if(isset($this->preset)) {
			foreach($this->preset as $name => $preset) {
				if($preset instanceof Preset) {
					$data[$name] = $preset->getParsedData();
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