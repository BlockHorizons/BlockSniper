<?php

namespace BlockHorizons\BlockSniper\sessions\owners;

use pocketmine\IPlayer;
use pocketmine\Player;
use pocketmine\Server;

class PlayerSessionOwner implements ISessionOwner{

	/** @var string */
	private $playerName = "";

	public function __construct(IPlayer $player){
		$this->playerName = strtolower($player->getName());
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