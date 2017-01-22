<?php

namespace Sandertv\BlockSniper;

use pocketmine\math\Vector3;
use pocketmine\item\Item;
use Sandertv\BlockSniper\tasks\UndoDiminishTask;

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
			$this->undoStore[$this->totalStores][$block->getId() . ":" . $block->getDamage() . "(" . $i . ")"] = [
				"x" => $block->x,
				"y" => $block->y,
				"z" => $block->z,
				"level" => $block->level->getName()
			];
			$i++;
		}
		unset($i);
		
		if(count($this->undoStore) === $this->getOwner()->settings->get("Maximum-Undo-Stores")) {
			$this->unsetFirstUndo(); // Unset the first undo to make sure the array won't get too big.
		}
		$this->getOwner()->getServer()->getScheduler()->scheduleDelayedTask(new UndoDiminishTask($this->getOwner()), 2400);
	}
	
	public function restoreLastUndo() {
		foreach($this->undoStore[max(array_keys($this->undoStore))] as $key => $block) {
			$Id = explode("(", $key);
			$blockId = $Id[0];
			$meta = explode(":", $blockId);
			$meta = $meta[1];
			$x = $block["x"];
			$y = $block["y"];
			$z = $block["z"];
			$finalBlock = Item::get($blockId, $meta)->getBlock();
			$finalBlock->setDamage((int) $meta !== null ? $meta : 0);
			$this->getOwner()->getServer()->getLevelByName($block["level"])->setBlock(new Vector3($x, $y, $z), $finalBlock, false, false);
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
