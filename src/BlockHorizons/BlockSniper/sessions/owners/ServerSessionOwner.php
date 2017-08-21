<?php

namespace BlockHorizons\BlockSniper\sessions\owners;

use pocketmine\Server;

class ServerSessionOwner implements ISessionOwner {

	/**
	 * @return Server
	 */
	public function getServer(): Server {
		return Server::getInstance();
	}

	/**
	 * @return string
	 */
	public function getName(): string {
		return "SERVER";
	}
}