<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockSniper\undo\async;

use BlockHorizons\BlockSniper\undo\IRedo;

class AsyncRedo extends AsyncRevert implements IRedo {

	/**
	 * @param array $chunks
	 *
	 * @return AsyncRevert
	 */
	public function getDetachedClass(array $chunks): AsyncRevert {
		return new AsyncUndo($chunks, $this->getPlayerName(), $this->getLevelId());
	}
}