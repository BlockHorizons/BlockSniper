<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\revert\async;

use BlockHorizons\BlockSniper\revert\IUndo;

class AsyncUndo extends AsyncRevert implements IUndo{

	/**
	 * @return AsyncRevert
	 */
	public function getDetachedClass() : AsyncRevert{
		return new AsyncRedo($this->getOldChunks(), $this->getModifiedChunks(), $this->getPlayerName(), $this->getLevelId());
	}
}