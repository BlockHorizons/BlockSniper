<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockSniper\git;

use pocketmine\block\Block;
use pocketmine\level\Level;

class Project {

	/** @var Level */
	private $level = null;
	/** @var int */
	private $commitIndexCounter = 0;
	/** @var GitCommit[] */
	private $commits = [];
	/** @var array */
	private $pulls = [];

	public function __construct(Level $level) {
		$this->level = $level;
	}

	/**
	 * @return int
	 */
	public function getNextCommitIndex(): int {
		return $this->commitIndexCounter++;
	}

	/**
	 * @return Level
	 */
	public function getLevel(): Level {
		return $this->level;
	}

	public function push(GitCommit $commit): bool {
		$commit->putPushTime(time());
	}

	/**
	 * @param GitCommit $commit
	 *
	 * @return Block[]
	 */
	public function getApplicableChanges(GitCommit $commit): array {
		if(!$commit->isPushed()) {
			return [];
		}
		$finalChanges = [];
		foreach($this->pulls as $time => $changes) {
			if($time > $commit->getCloneTime() && $time < $commit->getPushTime()) {
				foreach($changes as $block) {
					if(array_key_exists(Level::chunkHash($block->x >> 4, $block->z >> 4), $commit->getChunks())) {
						$finalChanges[] = $block;
					}
				}
			}
		}
		return $finalChanges;
	}

	/**
	 * @return bool
	 */
	public function shouldSaveChanges(): bool {
		foreach($this->commits as $commit) {
			if($commit->getCloneTime() < time() && !$commit->isPushed()) {
				return true;
			}
		}
		return false;
	}

	/**
	 * @param Block $block
	 *
	 * @return bool
	 */
	public function pullChange(Block $block): bool {
		if(!$this->shouldSaveChanges()) {
			return false;
		}
		$this->pulls[time()][] = $block;
		return true;
	}

	/**
	 * @param Block[] $blocks
	 *
	 * @return bool
	 */
	public function pullChanges(array $blocks): bool {
		if(!$this->shouldSaveChanges()) {
			return false;
		}
		foreach($blocks as $block) {
			$this->pullChange($block);
		}
		return true;
	}
}