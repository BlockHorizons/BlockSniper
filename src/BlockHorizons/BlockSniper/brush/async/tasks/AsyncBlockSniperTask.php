<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockSniper\brush\async\tasks;

use pocketmine\scheduler\AsyncTask;

abstract class AsyncBlockSniperTask extends AsyncTask {

	const TYPE_BRUSH = 0;
	const TYPE_REVERT = 1;
	const TYPE_COPY = 2;
	const TYPE_PASTE = 3;

	/** @var int */
	protected $taskType = self::TYPE_BRUSH;

	/**
	 * @return int
	 */
	public function getType(): int {
		return $this->taskType;
	}
}