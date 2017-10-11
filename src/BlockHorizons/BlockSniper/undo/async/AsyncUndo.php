<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockSniper\undo\async;

use BlockHorizons\BlockSniper\undo\IUndo;

class AsyncUndo extends AsyncRevert implements IUndo {

	/**
	 * @return AsyncRevert
	 */
	public function getDetachedClass(): AsyncRevert {
		return new AsyncRedo($this->getOldChunks(), $this->getModifiedChunks(), $this->getPlayerName(), $this->getLevelId());
	}
}