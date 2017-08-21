<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockSniper\undo;

class RevertStorer {

	/** @var IUndo[] */
	private $undoStack = [];
	/** @var int */
	private $maxRevertStores = 15;
	/** @var IRedo[][] */
	private $redoStack = [];

	/** @var int */
	private $lastUndo = 0;
	/** @var int */
	private $lastRedo = 0;

	public function __construct(int $maxRevertStores) {
		$this->maxRevertStores = $maxRevertStores;
	}

	/**
	 * @param int $type
	 * @param int $amount
	 */
	public function restoreLatestRevert(int $type, int $amount) {
		for($i = 0; $i < $amount; $i++) {
			if($type === Revert::TYPE_UNDO) {
				$revert = $this->undoStack[max(array_keys($this->undoStack))];
			} else {
				$revert = $this->redoStack[max(array_keys($this->redoStack))];
			}
			$detached = $revert->getDetached();
			$this->saveRevert($detached);
			$revert->restore();
			$this->unsetLatestRevert($type);
		}
	}

	/**
	 * @param Revert $revert
	 */
	public function saveRevert(Revert $revert) {
		$type = $revert instanceof IUndo ? Revert::TYPE_UNDO : Revert::TYPE_REDO;
		if($this->getTotalStores($type) === $this->maxRevertStores) {
			$this->unsetOldestRevert($type);
		}
		if($type === Revert::TYPE_UNDO) {
			$this->undoStack[] = $revert;
			$this->lastUndo = time();
		} else {
			$this->redoStack[] = $revert;
			$this->lastRedo = time();
		}
	}

	/**
	 * @param int $type
	 *
	 * @return int
	 */
	public function getTotalStores(int $type): int {
		if($type === Revert::TYPE_UNDO) {
			return count($this->undoStack);
		}
		return count($this->redoStack);
	}

	/**
	 * @param int $type
	 */
	public function unsetOldestRevert(int $type) {
		if($type === Revert::TYPE_UNDO) {
			unset($this->undoStack[min(array_keys($this->undoStack))]);
		} else {
			unset($this->redoStack[min(array_keys($this->undoStack))]);
		}
	}

	/**
	 * @param int $type
	 */
	public function unsetLatestRevert(int $type) {
		if($type === Revert::TYPE_UNDO) {
			unset($this->undoStack[max(array_keys($this->undoStack))]);
		} else {
			unset($this->redoStack[max(array_keys($this->undoStack))]);
		}
	}

	public function resetStorage() {
		$this->undoStack = [];
		$this->redoStack = [];
		$this->lastUndo = 0;
		$this->lastRedo = 0;
	}

	/**
	 * @return bool
	 */
	public function undoStorageExists(): bool {
		return !empty($this->undoStack);
	}

	/**
	 * @return bool
	 */
	public function redoStorageExists(): bool {
		return !empty($this->redoStack);
	}

	/**
	 * @return int
	 */
	public function getLastUndoActivity(): int {
		return (time() - $this->lastUndo);
	}

	/**
	 * @return int
	 */
	public function getLastRedoActivity(): int {
		return (time() - $this->lastRedo);
	}
}
