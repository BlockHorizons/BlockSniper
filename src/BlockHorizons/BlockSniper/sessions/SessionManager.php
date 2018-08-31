<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\sessions;

use BlockHorizons\BlockSniper\Loader;
use BlockHorizons\BlockSniper\sessions\owners\PlayerSessionOwner;
use pocketmine\IPlayer;
use pocketmine\Player;

class SessionManager {

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
			self::createPlayerSession($player, $plugin);
		}
		return self::$playerSessions[strtolower($player->getName())] ?? null;
	}

	/**
	 * @param IPlayer $player
	 * @param Loader  $loader
	 */
	public static function createPlayerSession(IPlayer $player, Loader $loader) : void{
		if(self::playerSessionExists($player)){
			return;
		}
		self::$playerSessions[strtolower($player->getName())] = new PlayerSession(new PlayerSessionOwner($player), $loader);
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
		if(self::playerSessionExists($player)){
			self::$playerSessions[strtolower($player->getName())]->close();
			unset(self::$playerSessions[strtolower($player->getName())]);
		}
	}
}