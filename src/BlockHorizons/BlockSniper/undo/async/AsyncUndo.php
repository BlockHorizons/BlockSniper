<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockSniper\undo\async;

use BlockHorizons\BlockSniper\undo\IUndo;

class AsyncUndo extends AsyncRevert implements IUndo {

	/**
	 * @param array  $chunks
	 * @param string $playerName
	 *
	 * @return AsyncRevert
	 */
	public function getDetachedClass(array $chunks): AsyncRevert {
		return new AsyncRedo($chunks, $this->getPlayerName(), $this->getLevelId());
	}
}