<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockSniper\git;

class GitCommit {

	/** @var int */
	private $cloneTime = 0;
	/** @var int */
	private $pushTime = -1;

	public function __construct(int $cloneTime = -1) {
		$this->cloneTime = $cloneTime;
		if($cloneTime < 0) {
			$this->cloneTime = time();
		}
	}

	/**
	 * @return int
	 */
	public function getCloneTime(): int {
		return $this->cloneTime;
	}

	/**
	 * @param int $pushTime
	 *
	 * @return GitCommit
	 */
	public function putPushTime(int $pushTime): self {
		$this->pushTime = $pushTime;

		return $this;
	}

	/**
	 * @return int
	 */
	public function getPushTime(): int {
		return $this->pushTime;
	}

	/**
	 * @return bool
	 */
	public function isPushed(): bool {
		return $this->pushTime >= 0;
	}
}