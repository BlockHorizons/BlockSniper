<?php

namespace Sandertv\BlockSniper\undo;

use pocketmine\block\Block;

class Undo {
	
	private $undoBlocks;
	private $storer;
	
	/**
	 * @param UndoStorer $storer
	 * @param Block[]    $undoBlocks
	 */
	public function __construct(UndoStorer $storer, array $undoBlocks) {
		$this->storer = $storer;
		$this->undoBlocks = $undoBlocks;
	}
	
	public function restore() {
		foreach($this->undoBlocks as $undoBlock) {
			$undoBlock->getLevel()->setBlock($undoBlock, $undoBlock, false, false);
		}
	}

	/**
	 * @return Redo
	 */
	public function getDetachedRedo(): Redo {
		$redoBlocks = [];
		foreach($this->undoBlocks as $undoBlock) {
			$redoBlocks[] = $undoBlock->getLevel()->getBlock($undoBlock);
		}

		return new Redo($this->storer, $redoBlocks);
	}
	
	/**
	 * @return int
	 */
	public function getBlockCount(): int {
		return count($this->undoBlocks);
	}
}