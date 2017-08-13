<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockSniper\undo;

use BlockHorizons\BlockSniper\Loader;
use pocketmine\Player;

class RevertStorer {

	/** @var Undo[][] */
	private $undoStack = [];

	/** @var Redo[][] */
	private $redoStack = [];
	/** @var array */
	private $lastUndo = [];
	/** @var array */
	private $lastRedo = [];
	/** @var Loader */
	private $loader = null;

	public function __construct(Loader $loader) {
		$this->loader = $loader;
	}

	/**
	 * @param int    $type
	 * @param int    $amount
	 * @param Player $player
	 */
	public function restoreLatestRevert(int $type, int $amount, Player $player) {
		for($i = 0; $i < $amount; $i++) {
			if($type === Revert::TYPE_UNDO) {
				$revert = $this->undoStack[$player->getName()][max(array_keys($this->undoStack[$player->getName()]))];
			} else {
				$revert = $this->redoStack[$player->getName()][max(array_keys($this->redoStack[$player->getName()]))];
			}
			$detached = $revert->getDetached();
			$this->saveRevert($detached, $player);
			$revert->restore();
			$this->unsetLatestRevert($player, $type);
		}
	}

	/**
	 * @param Revert $revert
	 * @param Player $player
	 */
	public function saveRevert(Revert $revert, Player $player) {
		$type = $revert instanceof Undo ? Revert::TYPE_UNDO : Revert::TYPE_REDO;
		if($this->getTotalStores($player, $type) === $this->getLoader()->getSettings()->getMaxUndoStores()) {
			$this->unsetOldestRevert($player, $type);
		}
		if($type === Revert::TYPE_UNDO) {
			$this->undoStack[$player->getName()][] = $revert;
			$this->lastUndo[$player->getName()] = time();
		} else {
			$this->redoStack[$player->getName()][] = $revert;
			$this->lastRedo[$player->getName()] = time();
		}
	}

	/**
	 * @param Player $player
	 * @param int    $type
	 *
	 * @return int
	 */
	public function getTotalStores(Player $player, int $type): int {
		if($type === Revert::TYPE_UNDO) {
			if(!isset($this->undoStack[$player->getName()])) {
				return 0;
			}
			return count($this->undoStack[$player->getName()]);
		}
		if(!isset($this->redoStack[$player->getName()])) {
			return 0;
		}
		return count($this->redoStack[$player->getName()]);
	}

	/**
	 * @return Loader
	 */
	public function getLoader(): Loader {
		return $this->loader;
	}

	/**
	 * @param Player $player
	 * @param int    $type
	 *
	 */
	public function unsetOldestRevert(Player $player, int $type) {
		if($type === Revert::TYPE_UNDO) {
			unset($this->undoStack[$player->getName()][min(array_keys($this->undoStack[$player->getName()]))]);
		} else {
			unset($this->redoStack[$player->getName()][min(array_keys($this->undoStack[$player->getName()]))]);
		}
	}

	/**
	 * @param Player $player
	 * @param int    $type
	 *
	 */
	public function unsetLatestRevert(Player $player, int $type) {
		if($type === Revert::TYPE_UNDO) {
			unset($this->undoStack[$player->getName()][max(array_keys($this->undoStack[$player->getName()]))]);
		} else {
			unset($this->redoStack[$player->getName()][max(array_keys($this->undoStack[$player->getName()]))]);
		}
	}

	public function resetStorage() {
		$this->undoStack = [];
		$this->redoStack = [];
		$this->lastUndo = [];
		$this->lastRedo = [];
	}

	/**
	 * @param Player $player
	 *
	 * @return bool
	 */
	public function undoStorageExists(Player $player): bool {
		if(!isset($this->undoStack[$player->getName()])) {
			return false;
		}
		if(!is_array($this->undoStack[$player->getName()]) || empty($this->undoStack[$player->getName()])) {
			return false;
		}
		return true;
	}

	/**
	 * @param Player $player
	 *
	 * @return bool
	 */
	public function redoStorageExists(Player $player): bool {
		if(!isset($this->redoStack[$player->getName()])) {
			return false;
		}
		if(!is_array($this->redoStack[$player->getName()]) || empty($this->redoStack[$player->getName()])) {
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
