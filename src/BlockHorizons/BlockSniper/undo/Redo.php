<?php

namespace BlockHorizons\BlockSniper\undo;

use pocketmine\block\Block;

class Redo {

	private $redoBlocks;
	private $blockCount;

	/**
	 * @param array $redoBlocks
	 * @param int   $blockCount
	 */
	public function __construct(array $redoBlocks, int $blockCount) {
		$this->redoBlocks = $redoBlocks;
		$this->blockCount = $blockCount;
	}

	public function restore() {
		foreach($this->redoBlocks as $redoBlock) {
			$redoBlock->getLevel()->setBlock($redoBlock, $redoBlock, false, false);
		}
	}

	/**
	 * @return array
	 */
	public function getDetachedUndo(): Undo {
		$undoBlocks = [];
		foreach($this->redoBlocks as $redoBlock) {
			$undoBlocks[] = $redoBlock->getLevel()->getBlock($redoBlock);
		}

		return new Undo($undoBlocks, $this->getBlockCount());
	}

	/**
	 * @return Block[]
	 */
	public function getBlocks(): array {
		return $this->redoBlocks;
	}

	/**
	 * @return int
	 */
	public function getBlockCount(): int {
		return $this->blockCount;
	}
}