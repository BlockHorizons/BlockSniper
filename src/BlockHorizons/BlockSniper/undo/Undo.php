<?php

namespace BlockHorizons\BlockSniper\undo;

use pocketmine\block\Block;

class Undo {
	
	private $undoBlocks;
	private $blockCount;

	/**
	 * @param array $undoBlocks
	 * @param int   $totalBlocks
	 */
	public function __construct(array $undoBlocks, int $blockCount) {
		$this->undoBlocks = $undoBlocks;
		$this->blockCount = $blockCount;
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

		return new Redo($redoBlocks, $this->getBlockCount());
	}

	/**
	 * @return Block[]
	 */
	public function getBlocks(): array {
		return $this->undoBlocks;
	}
	
	/**
	 * @return int
	 */
	public function getBlockCount(): int {
		return $this->blockCount;
	}
}