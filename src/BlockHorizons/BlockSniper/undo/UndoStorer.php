<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockSniper\undo;

use BlockHorizons\BlockSniper\Loader;
use pocketmine\Player;

class UndoStorer {

	/** @var Undo[][] */
	private $undoStore = [];

	/** @var Redo[][] */
	private $redoStore = [];

	private $lastUndo;
	private $lastRedo;
	private $loader;

	public function __construct(Loader $loader) {
		$this->loader = $loader;
	}

	/**
	 * @param int    $amount
	 * @param Player $player
	 */
	public function restoreLatestRedo(int $amount = 1, Player $player) {
		for($currentAmount = 0; $currentAmount < $amount; $currentAmount++) {
			$redo = $this->redoStore[$player->getName()][max(array_keys($this->redoStore[$player->getName()]))];
			$undo = $redo->getDetachedUndo();
			$this->saveUndo($undo, $player);

			$redo->restore();
			$this->unsetLatestRedo($player);
		}
	}

	/**
	 * @param Undo   $undo
	 * @param Player $player
	 */
	public function saveUndo(Undo $undo, Player $player) {
		$this->undoStore[$player->getName()][] = $undo;

		if($this->getTotalUndoStores($player) === $this->getLoader()->getSettings()->getMaxUndoStores()) {
			$this->unsetOldestUndo($player);
		}
		$this->lastUndo[$player->getName()] = time();
	}

	/**
	 * @param Player $player
	 *
	 * @return int
	 */
	public function getTotalUndoStores(Player $player): int {
		return count($this->undoStore[$player->getName()]);
	}

	/**
	 * @return Loader
	 */
	public function getLoader(): Loader {
		return $this->loader;
	}

	/**
	 * @param Player $player
	 */
	public function unsetOldestUndo(Player $player) {
		unset($this->undoStore[$player->getName()][min(array_keys($this->undoStore[$player->getName()]))]);
	}

	/**
	 * @param Player $player
	 */
	public function unsetLatestRedo(Player $player) {
		unset($this->redoStore[$player->getName()][max(array_keys($this->redoStore[$player->getName()]))]);
	}

	/**
	 * @param int    $amount
	 * @param Player $player
	 */
	public function restoreLatestUndo(int $amount = 1, Player $player) {
		for($currentAmount = 0; $currentAmount < $amount; $currentAmount++) {
			$undo = $this->undoStore[$player->getName()][max(array_keys($this->undoStore[$player->getName()]))];
			$redo = $undo->getDetachedRedo();
			$this->saveRedo($redo, $player);

			$undo->restore();
			$this->unsetLatestUndo($player);
		}
	}

	/**
	 * @param Redo   $redo
	 * @param Player $player
	 */
	public function saveRedo(Redo $redo, Player $player) {
		$this->redoStore[$player->getName()][] = $redo;

		if($this->getTotalRedoStores($player) === $this->getLoader()->getSettings()->getMaxUndoStores()) {
			$this->unsetOldestRedo($player);
		}
		$this->lastRedo[$player->getName()] = time();
	}

	/**
	 * @param Player $player
	 *
	 * @return int
	 */
	public function getTotalRedoStores(Player $player): int {
		return count($this->redoStore[$player->getName()]);
	}

	/**
	 * @param Player $player
	 */
	public function unsetOldestRedo(Player $player) {
		unset($this->redoStore[$player->getName()][min(array_keys($this->undoStore[$player->getName()]))]);
	}

	/**
	 * @param Player $player
	 */
	public function unsetLatestUndo(Player $player) {
		unset($this->undoStore[$player->getName()][max(array_keys($this->undoStore[$player->getName()]))]);
	}

	public function resetUndoStorage() {
		$this->undoStore = [];
		$this->redoStore = [];
		$this->lastUndo = null;
	}

	/**
	 * @param Player $player
	 *
	 * @return bool
	 */
	public function undoStorageExists(Player $player) {
		if(!isset($this->undoStore[$player->getName()])) {
			return false;
		}
		if(!is_array($this->undoStore[$player->getName()]) || empty($this->undoStore[$player->getName()])) {
			return false;
		}
		return true;
	}

	/**
	 * @param Player $player
	 *
	 * @return bool
	 */
	public function redoStorageExists(Player $player) {
		if(!isset($this->redoStore[$player->getName()])) {
			return false;
		}
		if(!is_array($this->redoStore[$player->getName()]) || empty($this->redoStore[$player->getName()])) {
			return false;
		}
		return true;
	}

	/**
	 * @param Player $player
	 *
	 * @return int
	 */
	public function getLastUndoActivity(Player $player): int {
		return (time() - $this->lastUndo[$player->getName()]);
	}

	/**
	 * @param Player $player
	 *
	 * @return int
	 */
	public function getLastRedoActivity(Player $player): int {
		return (time() - $this->lastRedo[$player->getName()]);
	}
}
