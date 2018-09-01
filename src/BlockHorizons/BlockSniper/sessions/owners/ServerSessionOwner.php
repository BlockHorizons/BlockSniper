<?php

namespace BlockHorizons\BlockSniper\sessions\owners;

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
}