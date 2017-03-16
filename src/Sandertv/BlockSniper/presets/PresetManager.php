<?php

namespace Sandertv\BlockSniper\presets;

use pocketmine\Player;
use Sandertv\BlockSniper\Loader;

class PresetManager {
	
	public $main;
	public $presetCreation = [];
	private $data;
	private $preset;
	
	public function __construct(Loader $main) {
		$this->main = $main;
		
		$this->data = yaml_parse_file($main->getDataFolder() . "presets.yml");
		
		foreach($this->data as $name => $data) {
			$this->addPreset($name);
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
			$this->data[$name]["size"],
			$this->data[$name]["hollow"],
			$this->data[$name]["blocks"],
			$this->data[$name]["obsolete"],
			$this->data[$name]["height"],
			$this->data[$name]["biome"]);
		unset($this->data[$name]);
	}
	
	/**
	 * @return Loader
	 */
	public function getOwner(): Loader {
		return $this->main;
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
	public function getCreationData(Player $player, string $key) {
		return $this->presetCreation[$player->getId()][$key];
	}
	
	/**
	 * @param Player $player
	 */
	public function cancelPresetCreationProcess(Player $player) {
		unset($this->presetCreation[$player->getId()]);
	}
}