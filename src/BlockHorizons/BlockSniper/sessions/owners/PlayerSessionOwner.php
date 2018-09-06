<?php

namespace BlockHorizons\BlockSniper\sessions\owners;

use pocketmine\Player;
use pocketmine\Server;

class PlayerSessionOwner implements ISessionOwner{

	/** @var string */
	private $playerName = "";

	public function __construct(string $playerName){
		$this->playerName = $playerName;
	}

	/**
	 * @return null|Player
	 */
	public function getPlayer() : ?Player{
		return Server::getInstance()->getPlayer($this->playerName);
	}

	/**
	 * @return string
	 */
	public function getPlayerName() : string{
		return $this->playerName;
	}

	/**
	 * @return string
	 */
	public function getName() : string{
		return $this->playerName;
	}
}