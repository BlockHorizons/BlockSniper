<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockSniper\undo;

use pocketmine\block\Block;

class Redo implements Revert {

	private $redoBlocks;

	/**
	 * @param Block[] $redoBlocks
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
	 * Should be called BEFORE the redo has been restored.
	 *
	 * @return Undo
	 */
	public function getDetachedUndo(): Undo {
		$undoBlocks = [];
		foreach($this->redoBlocks as $redoBlock) {
			$undoBlocks[] = $redoBlock->getLevel()->getBlock($redoBlock);
		}

		return new Undo($undoBlocks);
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
		return count($this->redoBlocks);
	}
}