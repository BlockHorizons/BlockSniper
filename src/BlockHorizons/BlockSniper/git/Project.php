<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockSniper\git;

use pocketmine\level\Level;

class Project {

	/** @var Level */
	private $level = null;
	/** @var int */
	private $tracker = 0;

	public function __construct(Level $level) {
		$this->level = $level;
	}

	/**
	 * @return Level
	 */
	public function getLevel(): Level {
		return $this->level;
	}

	/**
	 * @return int
	 */
	public function startTracking(array $chunks): int {
		return ++$this->tracker;
	}

	/**
	 * @return int
	 */
	public function stopTracking(): int {
		if($this->tracker === 0) {
			return 0;
		}
		return --$this->tracker;
	}

	/**
	 * @return bool
	 */
	public function isTracking(): bool {
		return $this->tracker > 0;
	}
}