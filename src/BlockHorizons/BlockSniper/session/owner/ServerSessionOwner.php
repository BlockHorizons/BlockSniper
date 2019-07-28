<?php

namespace BlockHorizons\BlockSniper\session\owner;

use GlobalLogger;
use pocketmine\Server;

class ServerSessionOwner implements ISessionOwner{

	/** @var int */
	private static $id = 0;

	public function __construct(){
		self::$id++;
	}

	/**
	 * @return Server
	 */
	public function getServer() : Server{
		return Server::getInstance();
	}

	/**
	 * @return int
	 */
	public function getId() : int{
		return self::$id;
	}

	/**
	 * @return string
	 */
	public function getName() : string{
		return "Server Session #" . self::$id;
	}

	/**
	 * @param string $message
	 */
	public function sendMessage(string $message) : void{
		GlobalLogger::get()->info($message);
	}
}