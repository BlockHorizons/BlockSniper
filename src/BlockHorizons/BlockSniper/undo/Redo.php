<?php

namespace BlockHorizons\BlockSniper\undo;

use pocketmine\block\Block;

class Redo {

	private $redoBlocks;

	/**
	 * @param Block[]    $redoBlocks
	 */
	public function __construct(array $redoBlocks) {
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