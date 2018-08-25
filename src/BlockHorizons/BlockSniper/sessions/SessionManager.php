<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\sessions;

use BlockHorizons\BlockSniper\brush\Brush;
use BlockHorizons\BlockSniper\brush\PropertyProcessor;
use BlockHorizons\BlockSniper\Loader;
use BlockHorizons\BlockSniper\sessions\owners\PlayerSessionOwner;
use BlockHorizons\BlockSniper\sessions\owners\ServerSessionOwner;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\IPlayer;
use pocketmine\level\Position;

class SessionManager implements Listener{

	/** @var PlayerSession[] */
	private static $playerSessions = [];
	/** @var Loader */
	private $loader = null;

	public function __construct(Loader $loader){
		$this->loader = $loader;
	}

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
	 * @param PlayerJoinEvent $event
	 *
	 * @return bool
	 */
	public function initialSessionJoin(PlayerJoinEvent $event) : bool{
		if(!$event->getPlayer()->hasPermission("blocksniper.command.brush")){
			return false;
		}
		$this->createPlayerSession($event->getPlayer());

		return true;
	}

	/**
	 * @param PlayerQuitEvent $event
	 *
	 * @return bool
	 */
	public function onQuit(PlayerQuitEvent $event) : bool{
		self::$playerSessions[$event->getPlayer()->getLowerCaseName()]->close();
		unset(self::$playerSessions[$event->getPlayer()->getLowerCaseName()]);

		return true;
	}

	/**
	 * @param IPlayer $player
	 *
	 * @return bool
	 */
	public function createPlayerSession(IPlayer $player) : bool{
		if(self::playerSessionExists($player)){
			return false;
		}
		self::$playerSessions[strtolower($player->getName())] = new PlayerSession(new PlayerSessionOwner($player), $this->getLoader());

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
	 * @return Loader
	 */
	public function getLoader() : Loader{
		return $this->loader;
	}
}