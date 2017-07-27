<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockSniper\operation;


use BlockHorizons\BlockSniper\Loader;

class Operator {

	/** @var History */
	private $history = null;
	/** @var Loader */
	private $loader = null;
	/** @var int */
	private $nextUoid = 0;
	/** @var null|Operation */
	private $running = null;
	/** @var Operation[] */
	private $stack = [];

	public function __construct(Loader $loader) {
		$this->loader = $loader;
		$this->history = new History($this, $loader->getDataFolder() . "history.yml");
		$this->nextUoid = $this->history->fetchFirstUoid() + 1;
	}

	/**
	 * @return Loader
	 */
	public function getLoader(): Loader {
		return $this->loader;
	}

	/**
	 * @return History
	 */
	public function getHistory(): History {
		return $this->history;
	}

	/**
	 * @return int
	 */
	public function generateUniqueOperationId(): int {
		return $this->nextUoid++;
	}

	/**
	 * @return int
	 */
	public function getCurrentUoid(): int {
		return $this->nextUoid;
	}

	/**
	 * @return bool
	 */
	public function isOperationRunning(): bool {
		return $this->running !== null;
	}

	/**
	 * @return Operation
	 */
	public function getRunningOperation(): Operation {
		return $this->running;
	}

	/**
	 * @param Operation $operation
	 *
	 * @return bool
	 */
	public function stack(Operation $operation): bool {
		if($operation->isWritten()) {
			return false;
		}
		$this->stack[] = $operation;
		return true;
	}
}