<?php

namespace Sandertv\BlockSniper\brush;

use pocketmine\Player;
use Sandertv\BlockSniper\Loader;

class BrushManager {
	
	private static $brush = [];
	
	public function __construct(Loader $main) {
		$this->main = $main;
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
	public function createBrush(Player $player): bool {
		if(isset(self::$brush[$player->getName()])) {
			return false;
		}
		self::$brush[$player->getName()] = new Brush($player, $this->main->getServer());
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
}