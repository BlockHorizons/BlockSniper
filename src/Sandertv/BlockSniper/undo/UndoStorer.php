<?php

namespace Sandertv\BlockSniper\undo;

use pocketmine\Player;
use Sandertv\BlockSniper\Loader;

class UndoStorer {
	
	private $undoStore = [];
	private $lastUndo;
	
	public function __construct(Loader $owner) {
		$this->owner = $owner;
	}
	
	/**
	 * @param array  $blocks
	 * @param Player $player
	 */
	public function saveUndo(array $blocks, Player $player) {
		$this->undoStore[$player->getName()][] = new Undo($blocks);
		
		if($this->getTotalUndoStores($player) === $this->getOwner()->getSettings()->get("Maximum-Undo-Stores")) {
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
	public function getOwner(): Loader {
		return $this->owner;
	}
	
	/**
	 * @param Player $player
	 */
	public function unsetOldestUndo(Player $player) {
		unset($this->undoStore[$player->getName()][min(array_keys($this->undoStore[$player->getName()]))]);
	}
	
	/**
	 * @param int    $amount
	 * @param Player $player
	 */
	public function restoreLatestUndo(int $amount = 1, Player $player) {
		for($currentAmount = 0; $currentAmount < $amount; $currentAmount++) {
			$undo = $this->undoStore[$player->getName()][max(array_keys($this->undoStore[$player->getName()]))];
			$undo->restore();
			
			$this->unsetLatestUndo($player);
		}
	}
	
	/**
	 * @param Player $player
	 */
	public function unsetLatestUndo(Player $player) {
		unset($this->undoStore[$player->getName()][max(array_keys($this->undoStore[$player->getName()]))]);
	}
	
	public function resetUndoStorage() {
		$this->undoStore = [];
		$this->totalStores = 0;
	}
	
	/**
	 * @param Player $player
	 *
	 * @return bool
	 */
	public function undoStorageExists(Player $player) {
		if(!is_array($this->undoStore[$player->getName()]) || empty($this->undoStore[$player->getName()])) {
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
}
