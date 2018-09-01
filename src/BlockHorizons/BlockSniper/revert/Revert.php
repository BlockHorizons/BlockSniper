<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\revert;

use BlockHorizons\BlockSniper\revert\async\AsyncRevert;
use BlockHorizons\BlockSniper\revert\sync\SyncRevert;

abstract class Revert{

	const TYPE_UNDO = 0;
	const TYPE_REDO = 1;

	/** @var string */
	protected $playerName = "";

	public function __construct(string $playerName){
		$this->playerName = $playerName;
	}

	/**
	 * @return string
	 */
	public function getPlayerName() : string{
		return $this->playerName;
	}

	/**
	 * @param string $name
	 *
	 * @return Revert
	 */
	public function setPlayerName(string $name) : self{
		$this->playerName = $name;

		return $this;
	}

	/**
	 * @return SyncRevert|AsyncRevert
	 */
	public abstract function getDetached() : Revert;
}