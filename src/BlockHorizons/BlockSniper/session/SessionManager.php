<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\session;

use BlockHorizons\BlockSniper\Loader;
use BlockHorizons\BlockSniper\session\owner\PlayerSessionOwner;
use pocketmine\player\Player;

class SessionManager{

	/** @var PlayerSession[] */
	private static $playerSessions = [];

	public static function close() : void{
		foreach(self::$playerSessions as $session){
			$session->close();
		}
	}

	/**
	 * @param Player $player
	 *
	 * @return PlayerSession|null
	 */
	public static function getPlayerSession(Player $player) : ?PlayerSession{
		if($player->hasPermission("blocksniper.command.brush")){
			/** @var Loader $plugin */
			$plugin = $player->getServer()->getPluginManager()->getPlugin("BlockSniper");
			self::createPlayerSession($player->getName(), $plugin);
		}

		return self::$playerSessions[$player->getName()] ?? null;
	}

	/**
	 * @param string $playerName
	 * @param Loader $loader
	 */
	public static function createPlayerSession(string $playerName, Loader $loader) : void{
		if(self::playerSessionExists($playerName)){
			return;
		}
		self::$playerSessions[$playerName] = new PlayerSession(new PlayerSessionOwner($playerName), $loader);
	}

	/**
	 * @param string $playerName
	 *
	 * @return bool
	 */
	public static function playerSessionExists(string $playerName) : bool{
		return isset(self::$playerSessions[$playerName]);
	}

	/**
	 * @param string $playerName
	 */
	public static function closeSession(string $playerName) : void{
		if(self::playerSessionExists($playerName)){
			self::$playerSessions[$playerName]->close();
			unset(self::$playerSessions[$playerName]);
		}
	}
}