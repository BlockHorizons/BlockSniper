<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockSniper\undo\sync;

use BlockHorizons\BlockSniper\undo\IUndo;

class SyncUndo extends SyncRevert implements IUndo {

	/**
	 * @param array  $blocks
	 * @param string $playerName
	 *
	 * @return SyncRevert
	 */
	public function getDetachedClass(array $blocks, string $playerName): SyncRevert {
		return new SyncRedo($blocks, $playerName);
	}
}