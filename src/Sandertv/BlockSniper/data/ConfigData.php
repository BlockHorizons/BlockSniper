<?php

namespace Sandertv\BlockSniper\data;

use pocketmine\utils\Config;
use Sandertv\BlockSniper\Loader;

class ConfigData {
	
	public $settings = [];
	
	public function __construct(Loader $plugin) {
		$this->plugin = $plugin;
		
		$this->collectSettings();
	}
	
	/**
	 * @return Loader
	 */
	public function getOwner(): Loader {
		return $this->plugin;
	}
	
	public function collectSettings() {
		$cfg = yaml_parse_file($this->getOwner()->getDataFolder() . "settings.yml");
		$this->settings = [
			"Message-Language" => $cfg["Message-Language"],
			"Brush-Item" => $cfg["Brush-Item"],
			"Maximum-Radius" => $cfg["Maximum-Radius"],
			"Maximum-Undo-Stores" => $cfg["Maximum-Undo-Stores"],
			"Reset-Decrement-Brush" => $cfg["Reset-Decrement-Brush"]
		];
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
}