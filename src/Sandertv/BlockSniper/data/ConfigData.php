<?php

namespace Sandertv\BlockSniper\data;

use pocketmine\utils\TextFormat as TF;
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
		@$this->settings = [
			"Configuration-Version" => $cfg["Configuration-Version"],
			"Auto-Configuration-Update" => $cfg["Auto-Configuration-Update"] ?? true,
			"Message-Language" => $cfg["Message-Language"] ?? "",
			"Brush-Item" => $cfg["Brush-Item"] ?? 396,
			"Maximum-Radius" => $cfg["Maximum-Radius"] ?? 15,
			"Maximum-Undo-Stores" => $cfg["Maximum-Undo-Stores"] ?? 15,
			"Reset-Decrement-Brush" => $cfg["Reset-Decrement-Brush"] ?? true,
			"Maximum-Clone-Size" => $cfg["Maximum-Clone-Size"] ?? 60,
			"Save-Brush-Properties" => $cfg["Save-Brush-Properties"] ?? true,
			"Drop-Leafblower-Plants" => $cfg["Drop-Leafblower-Plants"] ?? true
		];
		if($cfg["Configuration-Version"] !== Loader::CONFIGURATION_VERSION) {
			$autoUpdate = $cfg["Auto-Configuration-Update"];
			$this->getOwner()->getLogger()->info(TF::AQUA . "[BlockSniper] A new Configuration version was found. " . ($autoUpdate ? "Updating Configuration file..." : null));
			if($autoUpdate) {
				$this->updateConfig();
			}
		} else {
			$this->getOwner()->getLogger()->info(TF::AQUA . "[BlockSniper] No new Configuration version found, Configuration is up to date.");
		}
	}
	
	/**
	 * @return Loader
	 */
	public function getOwner(): Loader {
		return $this->plugin;
	}
	
	public function updateConfig() {
		unlink($this->getOwner()->getDataFolder() . "settings.yml");
		$this->settings["Configuration-Version"] = Loader::CONFIGURATION_VERSION;
		yaml_emit_file($this->getOwner()->getDataFolder() . "settings.yml", $this->settings);
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