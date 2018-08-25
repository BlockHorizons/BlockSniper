<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\sessions;

use BlockHorizons\BlockSniper\Loader;
use BlockHorizons\BlockSniper\sessions\owners\PlayerSessionOwner;
use pocketmine\IPlayer;

class SessionManager {

	/** @var PlayerSession[] */
	private static $playerSessions = [];

	public function close() : void{
		foreach(self::$playerSessions as $session){
			$session->close();
		}
	}

	/**
	 * @param IPlayer $player
	 *
	 * @return PlayerSession|null
	 */
	public static function getPlayerSession(IPlayer $player) : ?PlayerSession{
		return self::$playerSessions[strtolower($player->getName())] ?? null;
	}

	/**
	 * @param IPlayer $player
	 * @param Loader  $loader
	 *
	 * @return bool
	 */
	public static function createPlayerSession(IPlayer $player, Loader $loader) : bool{
		if(self::playerSessionExists($player)){
			return false;
		}
		self::$playerSessions[strtolower($player->getName())] = new PlayerSession(new PlayerSessionOwner($player), $loader);

		return true;
	}

	/**
	 * @param IPlayer $player
	 *
	 * @return bool
	 */
	public static function playerSessionExists(IPlayer $player) : bool{
		return isset(self::$playerSessions[strtolower($player->getName())]);
	}

	/**
	 * @param IPlayer $player
	 */
	public static function closeSession(IPlayer $player) : void{
		self::$playerSessions[strtolower($player->getName())]->close();
		unset(self::$playerSessions[strtolower($player->getName())]);
	}
}