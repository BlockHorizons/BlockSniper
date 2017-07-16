<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockSniper\worker;

use pocketmine\Player;

class TaskWorker {

	private $occupation;
	private $id;
	private $player;

	public function __construct(int $id, Player $player) {
		$this->id = $id;
		$this->player = $player;
	}

	/**
	 * @return string
	 */
	public function getOccupation(): string {
		return $this->occupation;
	}

	/**
	 * @param string $occupation
	 */
	public function setOccupation(string $occupation) {
		$this->occupation = $occupation;
	}

	/**
	 * @return bool
	 */
	public function clearOccupation(): bool {
		if($this->isOccupied()) {
			$this->occupation = null;
			return true;
		}
		return false;
	}

	/**
	 * @return bool
	 */
	public function isOccupied(): bool {
		return $this->occupation !== null;
	}

	/**
	 * @return int
	 */
	public function getWorkerId(): int {
		return $this->id;
	}

	/**
	 * @return Player
	 */
	public function getPlayer(): Player {
		return $this->player;
	}
}