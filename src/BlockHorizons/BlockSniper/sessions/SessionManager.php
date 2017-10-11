<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockSniper\sessions;

use BlockHorizons\BlockSniper\brush\Brush;
use BlockHorizons\BlockSniper\brush\PropertyProcessor;
use BlockHorizons\BlockSniper\Loader;
use BlockHorizons\BlockSniper\sessions\owners\PlayerSessionOwner;
use BlockHorizons\BlockSniper\sessions\owners\ServerSessionOwner;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\IPlayer;
use pocketmine\level\Position;

class SessionManager implements Listener {

	/** @var Loader */
	private $loader = null;
	/** @var int */
	private $serverSessionCounter = 0;
	/** @var PlayerSession[] */
	private static $playerSessions = [];
	/** @var ServerSession[] */
	private static $serverSessions = [];

	public function __construct(Loader $loader) {
		$this->loader = $loader;
		//$this->fetchServerSessions($loader);
	}

	/**
	 * @param Loader $loader
	 *
	 * @return bool
	 */
	private function createInitialSessionFile(Loader $loader): bool {
		if(!file_exists($loader->getDataFolder() . "serverSessions.json")) {
			file_put_contents($loader->getDataFolder() . "serverSessions.json", json_encode([
				[
					"targetBlock" => [
						"level" => "MyWorld",
						"x" => 256,
						"y" => 128,
						"z" => 256
					],
					"brush" => (new Brush(""))->jsonSerialize(),
					"name" => "ExampleGlobalBrush"
				]
			]));
			return true;
		}
		return false;
	}

	/**
	 * @param Loader $loader
	 */
	public function fetchServerSessions(Loader $loader): void {
		if(!file_exists($loader->getDataFolder() . "serverSessions.json")) {
			$this->createInitialSessionFile($loader);
		}
		foreach(json_decode(file_get_contents($loader->getDataFolder() . "serverSessions.json"), true) as $session) {
			if(($level = $loader->getServer()->getLevelByName($session["targetBlock"]["level"])) === null) {
				continue;
			}
			$i = $session["targetBlock"];
			$position = new Position((int) $i["x"], (int) $i["y"], (int) $i["z"], $level);
			self::$serverSessions[$id = $this->serverSessionCounter++] = new ServerSession(new ServerSessionOwner(), $loader);
			self::$serverSessions[$id]->setTargetBlock($position);

			$processor = new PropertyProcessor(self::$serverSessions[$id], $loader);
			foreach($session["brush"] as $property => $value) {
				$processor->process($property, $value);
			}
			self::$serverSessions[$id]->setName($session["name"]);
		}
	}

	/**
	 * @return ServerSession[]
	 */
	public function getServerSessions(): array {
		return self::$serverSessions;
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
		if(self::playerSessionExists($player)) {
			return false;
		}
		self::$playerSessions[strtolower($player->getName())] = new PlayerSession(new PlayerSessionOwner($player), $this->getLoader());
		return true;
	}

	/**
	 * @param IPlayer $player
	 *
	 * @return PlayerSession|null
	 */
	public static function getPlayerSession(IPlayer $player): ?PlayerSession {
		return self::$playerSessions[strtolower($player->getName())] ?? null;
	}

	/**
	 * @param IPlayer $player
	 *
	 * @return bool
	 */
	public static function playerSessionExists(IPlayer $player): bool {
		return isset(self::$playerSessions[strtolower($player->getName())]);
	}
}