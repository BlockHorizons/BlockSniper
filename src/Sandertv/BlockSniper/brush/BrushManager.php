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
			if(is_file($main->getDataFolder() . "brushes.json")) {
				$brushesSerialized = file_get_contents($main->getDataFolder() . "brushes.json");
				$brushes = json_decode($brushesSerialized);
				unlink($main->getDataFolder() . "brushes.json");
			}
			if(!empty($brushes)) {
				foreach($brushes as $playerName => $brush) {
					self::$brush[$playerName] = json_decode($brush);
					$main->getLogger()->debug(TF::GREEN . "Brush of player " . $playerName . " has been restored.");
				}
			}
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
		self::$brush[$player->getName()] = new Brush($player->getName(), $this->getPlugin()->getServer());
		return true;
	}
	
	/**
	 * @return Loader
	 */
	public function getPlugin(): Loader {
		return $this->main;
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
		foreach(self::$brush as $playerName => $brush) {
			$data[$playerName] = json_encode($brush, JSON_FORCE_OBJECT);
		}
		$data = json_encode($data);
		file_put_contents($this->getPlugin()->getDataFolder() . "brushes.json", $data);
	}
}
