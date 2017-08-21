<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockSniper\sessions;

use BlockHorizons\BlockSniper\Loader;
use BlockHorizons\BlockSniper\sessions\owners\PlayerSessionOwner;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\IPlayer;

class SessionManager implements Listener {

	/** @var Loader */
	private $loader = null;
	/** @var Session[] */
	private static $playerSessions = [];

	public function __construct(Loader $loader) {
		$this->loader = $loader;
	}

	/**
	 * @return Loader
	 */
	public function getLoader(): Loader {
		return $this->loader;
	}

	/**
	 * @param PlayerJoinEvent $event
	 *
	 * @return bool
	 */
	public function initialSessionJoin(PlayerJoinEvent $event): bool {
		if(!$event->getPlayer()->hasPermission("blocksniper.command.brush")) {
			return false;
		}
		$this->createPlayerSession($event->getPlayer());
		return true;
	}

	/**
	 * @param IPlayer $player
	 *
	 * @return bool
	 */
	public function createPlayerSession(IPlayer $player): bool {
		if($this->playerSessionExists($player)) {
			return false;
		}
		self::$playerSessions[strtolower($player->getName())] = new PlayerSession(new PlayerSessionOwner($player), $this->getLoader());
		return true;
	}

	/**
	 * @param IPlayer $player
	 *
	 * @return PlayerSession
	 */
	public static function getPlayerSession(IPlayer $player): PlayerSession {
		return self::$playerSessions[strtolower($player->getName())];
	}

	/**
	 * @param IPlayer $player
	 *
	 * @return bool
	 */
	public function playerSessionExists(IPlayer $player): bool {
		return isset(self::$playerSessions[strtolower($player->getName())]);
	}
}