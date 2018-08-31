<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\revert\sync;

use BlockHorizons\BlockSniper\revert\IRedo;

class SyncRedo extends SyncRevert implements IRedo{

	/**
	 * @param array  $blocks
	 * @param string $playerName
	 *
	 * @return SyncRevert
	 */
	public function getDetachedClass(array $blocks, string $playerName) : SyncRevert{
		return new SyncUndo($blocks, $playerName);
	}
}