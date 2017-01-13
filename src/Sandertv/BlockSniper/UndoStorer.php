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
		$this->totalStores++;
		foreach($blocks as $block) {
			$this->undoStore[$this->totalStores][$block->getId() . "(" . $i . ")"] = [
				"x" => $block->x,
				"y" => $block->y,
				"z" => $block->z,
				"level" => $block->level->getName()
			];
			$i++;
		}
	}
	
	public function restoreLastUndo() {
		foreach($this->undoStore[max(array_keys($this->undoStore))] as $key => $block) {
			$Id = explode("(", $key);
			$blockId = $Id[0];
			$x = $block["x"];
			$y = $block["y"];
			$z = $block["z"];
			$this->getOwner()->getServer()->getLevelByName($block["level"])->setBlock(new Vector3($x, $y, $z), Block::get((int)$blockId), false, false);
		}
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
		if($this->totalStores === 0 || !is_array($this->undoStore) || empty($this->undoStore)) {
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
