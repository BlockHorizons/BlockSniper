<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockSniper\brush\async\tasks;

use BlockHorizons\BlockSniper\undo\Redo;
use BlockHorizons\BlockSniper\undo\Revert;
use BlockHorizons\BlockSniper\undo\Undo;

class RevertTask extends AsyncBlockSniperTask {

	/** @var int */
	protected $type = self::TYPE_REVERT;
	/** @var Undo|Redo */
	private $revert = null;

	public function __construct(Revert $revert) {
		$this->revert = $revert;
	}

	public function onRun() {
		// TODO: Implement onRun() method.
	}
}