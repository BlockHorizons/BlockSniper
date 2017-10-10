<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockSniper\git;

use pocketmine\level\Level;

class Project {

	/** @var Level */
	private $level = null;
	/** @var GitCommit[] */
	private $commits = [];

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
	 * @param GitCommit $commit
	 */
	public function push(GitCommit $commit): void {
		$this->commits[] = $commit;
	}
}