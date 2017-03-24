<?php

namespace Sandertv\BlockSniper\brush;

use pocketmine\Player;
use pocketmine\utils\TextFormat as TF;
use Sandertv\BlockSniper\Loader;

class BrushManager {
	
	private static $brush = [];
	
	public function __construct(Loader $main) {
		$this->main = $main;
		if($main->getSettings()->get("Save-Brush-Properties")) {
			$brushes = [];
			if(is_file($main->getDataFolder() . "brushes.yml")) {
				$brushes = yaml_parse_file($main->getDataFolder() . "brushes.yml");
				unlink($main->getDataFolder() . "brushes.yml");
			}
			if(!empty($brushes)) {
				foreach($brushes as $playerName => $brush) {
					self::$brush[$playerName] = unserialize($brush);
					$main->getLogger()->debug(TF::GREEN . "Brush of player " . $playerName . " has been restored.");
				}
			}
			var_dump(self::$brush);
			$main->getLogger()->info(TF::GREEN . "All brushes have been restored.");
		}
	}
	
	/**
	 * @param Player $player
	 *
	 * @return Brush|null
	 */
	public static function get(Player $player) {
		if(isset(self::$brush[$player->getName()])) {
			return self::$brush[$player->getName()];
		}
		return null;
	}
	
	/**
	 * @param Player $player
	 *
	 * @return bool
	 */
	public function createBrush(Player $player): bool {
		if(isset(self::$brush[$player->getName()])) {
			return false;
		}
		self::$brush[$player->getName()] = new Brush($player->getName(), $this->main->getServer());
		return true;
	}
	
	/**
	 * @param Player $player
	 *
	 * @return bool
	 */
	public function resetBrush(Player $player): bool {
		if(isset(self::$brush[$player->getName()])) {
			unset(self::$brush[$player->getName()]);
			return true;
		}
		return false;
	}
	
	public function storeBrushesToFile() {
		$data = [];
		if(!empty(self::$brush)) {
			foreach(self::$brush as $playerName => $brush) {
				$data[$playerName] = serialize($brush);
			}
			yaml_emit_file($this->getPlugin()->getDataFolder() . "brushes.yml", $data);
		}
	}
	
	/**
	 * @return Loader
	 */
	public function getPlugin(): Loader {
		return $this->main;
	}
}