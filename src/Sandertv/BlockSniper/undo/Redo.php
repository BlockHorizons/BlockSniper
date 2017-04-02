<?php

namespace Sandertv\BlockSniper\undo;

use pocketmine\block\Block;

class Redo {

	private $redoBlocks;
	private $storer;

	/**
	 * @param UndoStorer $storer
	 * @param Block[]    $redoBlocks
	 */
	public function __construct(UndoStorer $storer, array $redoBlocks) {
		$this->storer = $storer;
		$this->redoBlocks = $redoBlocks;
	}

	public function restore() {
		foreach($this->redoBlocks as $redoBlock) {
			$redoBlock->getLevel()->setBlock($redoBlock, $redoBlock, false, false);
		}
	}

	/**
	 * @return int
	 */
	public function getBlockCount(): int {
		return count($this->redoBlocks);
	}
}