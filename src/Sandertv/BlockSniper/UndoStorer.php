<?php

namespace Sandertv\BlockSniper;

use pocketmine\item\Item;
use pocketmine\math\Vector3;
use pocketmine\Player;

class UndoStorer {
	
	private $totalStores;
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
		$i = 0;
		$this->totalStores++;
		foreach($blocks as $block) {
			$this->undoStore[$player->getName()][$this->totalStores][$block->getId() . ":" . $block->getDamage() . "(" . $i . ")"] = [
				"x" => $block->x,
				"y" => $block->y,
				"z" => $block->z,
				"level" => $block->level->getName()
			];
			$i++;
		}
		unset($i);
		
		if($this->getTotalUndoStores($player) === $this->getOwner()->getSettings()->get("Maximum-Undo-Stores")) {
			$this->unsetFirstUndo($player);
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
	public function unsetFirstUndo(Player $player) {
		unset($this->undoStore[$player->getName()][min(array_keys($this->undoStore))]);
	}
	
	/**
	 * @param int    $amount
	 * @param Player $player
	 */
	public function restoreLastUndo(int $amount = 1, Player $player) {
		for($currentAmount = 0; $currentAmount < $amount; $currentAmount++) {
			foreach($this->undoStore[$player->getName()][max(array_keys($this->undoStore))] as $key => $block) {
				$Id = explode("(", $key);
				$blockId = $Id[0];
				$meta = explode(":", $blockId);
				$meta = $meta[1];
				$x = $block["x"];
				$y = $block["y"];
				$z = $block["z"];
				$finalBlock = Item::get($blockId, $meta)->getBlock();
				$finalBlock->setDamage((int)$meta !== null ? $meta : 0);
				$this->getOwner()->getServer()->getLevelByName($block["level"])->setBlock(new Vector3($x, $y, $z), $finalBlock, false, false);
			}
			$this->unsetLastUndo($player);
		}
	}
	
	/**
	 * @param Player $player
	 */
	public function unsetLastUndo(Player $player) {
		unset($this->undoStore[$player->getName()][max(array_keys($this->undoStore))]);
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
	public function getLastUndoBlockAmount(Player $player) {
		return count($this->undoStore[$player->getName()][max(array_keys($this->undoStore))]);
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
