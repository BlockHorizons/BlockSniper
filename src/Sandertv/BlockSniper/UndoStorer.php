<?php

namespace Sandertv\BlockSniper;

use pocketmine\math\Vector3;
use pocketmine\block\Block;

class UndoStorer {
	
	public $totalStores = 0;
	public $undoStore = [];
	
	public function __construct(Loader $owner) {
		$this->owner = $owner;
	}
	
	/**
	 * @return Loader
	 */
	public function getOwner(): Loader {
		return $this->owner;
	}
	
	/**
	 * @param array $blocks
	 */
	public function saveUndo(array $blocks) {
		$i = 0;
		$this->getOwner()->getLogger()->info("Starting save process...");
		foreach($blocks as $block) {
			$this->undoStore[$this->totalStores][$block->getId() . "($i)"] = [
				"x" => $block->x,
				"y" => $block->y,
				"z" => $block->z,
				"level" => $block->level->getName()
			];
			$i += 1;
		}
		unset($i);
		if($this->totalStores >= $this->getOwner()->settings->get("Maximum-Undo-Stores")) {
			$this->unsetFirstUndo();
		}
		$this->totalStores += 1;
		$this->getOwner()->getLogger()->info("Saved undo...");
	}
	
	public function restoreLastUndo() {
		$this->getOwner()->getLogger()->info("Restoring undo save...");
		foreach($this->undoStore[max(array_keys($this->undoStore))] as $block) {
			$Id = explode("(", $block);
			$blockId = $Id[0];
			$x = $this->undoStore[max(array_keys($this->undoStore))][$block]["x"];
			$y = $this->undoStore[max(array_keys($this->undoStore))][$block]["y"];
			$z = $this->undoStore[max(array_keys($this->undoStore))][$block]["z"];
			$this->getOwner()->getServer()->getLevelByName($this->undoStore[max(array_keys($this->undoStore))][$block]["level"])->setBlock(new Vector3($x, $y, $z), Block::get((int)$blockId), false, false);
		}
		$this->getOwner()->getLogger()->info("Restoring succesful...");
		$this->unsetLastUndo();
	}
	
	public function unsetLastUndo() {
		unset($this->undoStore[max(array_keys($this->undoStore))]);
	}
	
	public function unsetFirstUndo() {
		unset($this->undoStore[min(array_keys($this->undoStore))]);
	}
	
	public function resetUndoStorage() {
		$this->undoStore = [];
		$this->totalStores = 0;
	}
	
	/**
	 * @return bool
	 */
	public function undoStorageExists() {
		$this->getOwner()->getLogger()->info("Checking undo storage existance...");
		if($this->totalStores === 0 && empty($this->undoStore)) {
			return false;
		}
		return true;
	}
	
	/**
	 * @return int
	 */
	public function getLastUndoBlockAmount() {
		return count($this->undoStore[max(array_keys($this->undoStore))]);
	}
}