<?php

namespace BlockHorizons\BlockSniper\worker;

use BlockHorizons\BlockSniper\Loader;
use pocketmine\Player;

class WorkerManager {

	private $loader;

	/** @var TaskWorker[] */
	private $workers = [];
	private $nextId = 1;

	public function __construct(Loader $loader) {
		$this->loader = $loader;
	}

	/**
	 * @param Player $player
	 * @param string $occupation
	 *
	 * @return mixed
	 */
	public function scheduleWorker(Player $player, string $occupation) {
		if(!$this->hasWorkerAvailable($player)) {
			return false;
		}
		$worker = $this->getFreeWorker($player);
		$worker->setOccupation($occupation);
		return $worker->getWorkerId();
	}

	/**
	 * @param Player $player
	 *
	 * @return bool
	 */
	public function hasWorkerAvailable(Player $player): bool {
		foreach($this->workers as $worker) {
			if($worker->getPlayer()->getName() === $player->getName()) {
				if(!$worker->isOccupied()) {
					return true;
				}
			}
		}
		return false;
	}

	/**
	 * @param Player $player
	 *
	 * @return TaskWorker
	 */
	public function getFreeWorker(Player $player): TaskWorker {
		if(!$this->hasWorkerAvailable($player)) {
			return null;
		}
		foreach($this->workers as $worker) {
			if($worker->getPlayer()->getName() === $player->getName()) {
				if(!$worker->isOccupied()) {
					return $worker;
				}
			}
		}
		return null;
	}

	/**
	 * @param int $id
	 *
	 * @return TaskWorker
	 */
	public function getWorker(int $id): TaskWorker {
		if(!isset($this->workers[$id])) {
			return null;
		}
		return $this->workers[$id];
	}

	/**
	 * @param int $id
	 *
	 * @return bool
	 */
	public function clearWorker(int $id): bool {
		if(!isset($this->workers[$id])) {
			return false;
		}
		unset($this->workers[$id]);
		return true;
	}

	/**
	 * @param int $id
	 *
	 * @return bool
	 */
	public function workerExists(int $id): bool {
		return isset($this->workers[$id]);
	}

	/**
	 * @param Player $player
	 *
	 * @return bool
	 */
	public function addWorkers(Player $player): bool {
		if($this->hasWorkerAvailable($player)) {
			return false;
		}
		for($i = 0; $i < $this->getLoader()->getSettings()->getTickSpreadWorkers(); $i++) {
			$worker = new TaskWorker($this->nextId, $player);
			$this->workers[$worker->getWorkerId()] = $worker;
			$this->nextId++;
		}
		return true;
	}

	/**
	 * @return Loader
	 */
	public function getLoader(): Loader {
		return $this->loader;
	}
}