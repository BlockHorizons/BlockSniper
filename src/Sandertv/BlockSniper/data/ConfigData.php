<?php

namespace Sandertv\BlockSniper\data;

use Sandertv\BlockSniper\Loader;

class ConfigData {
	
	private $settings = [];
	private $plugin;
	
	public function __construct(Loader $plugin) {
		$this->plugin = $plugin;
		
		$this->collectSettings();
	}
	
	public function collectSettings() {
		$cfg = yaml_parse_file($this->getOwner()->getDataFolder() . "settings.yml");
		$this->settings = [
			"Message-Language" => $cfg["Message-Language"],
			"Brush-Item" => $cfg["Brush-Item"],
			"Maximum-Radius" => $cfg["Maximum-Radius"],
			"Maximum-Undo-Stores" => $cfg["Maximum-Undo-Stores"],
			"Reset-Decrement-Brush" => $cfg["Reset-Decrement-Brush"],
			"Maximum-Clone-Size" => $cfg["Maximum-Clone-Size"]
		];
	}
	
	/**
	 * @return Loader
	 */
	public function getOwner(): Loader {
		return $this->plugin;
	}
	
	/**
	 * @param string $key
	 *
	 * @return mixed|null
	 */
	public function get(string $key) {
		if(isset($this->settings[$key])) {
			return $this->settings[$key];
		}
		return null;
	}
	
	/**
	 * @param string $key
	 * @param        $value
	 */
	public function set(string $key, $value) {
		$this->settings[$key] = $value;
	}
	
	public function save() {
		yaml_emit_file($this->plugin->getDataFolder() . "settings.yml", $this->settings);
	}
}