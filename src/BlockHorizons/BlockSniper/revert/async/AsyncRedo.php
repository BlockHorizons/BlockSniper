<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\revert\async;

use BlockHorizons\BlockSniper\revert\IRedo;

class AsyncRedo extends AsyncRevert implements IRedo{

	/**
	 * @return AsyncRevert
	 */
	public function getDetachedClass() : AsyncRevert{
		return new AsyncUndo($this->getOldChunks(), $this->getModifiedChunks(), $this->getPlayerName(), $this->getLevelId());
	}
}