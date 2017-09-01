<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockSniper\brush\async\tasks;

use BlockHorizons\BlockSniper\Loader;
use pocketmine\scheduler\AsyncTask;
use pocketmine\Server;

abstract class AsyncBlockSniperTask extends AsyncTask {

	const TYPE_BRUSH = 0;
	const TYPE_REVERT = 1;
	const TYPE_COPY = 2;
	const TYPE_PASTE = 3;

	/** @var int */
	protected $taskType = self::TYPE_BRUSH;
	/** @var bool */
	private $aborted = false;

	/**
	 * @return int
	 */
	public function getType(): int {
		return $this->taskType;
	}

	/**
	 * @return bool
	 */
	public function isAborted(): bool {
		return $this->aborted;
	}

	/**
	 * @param bool $value
	 */
	public function setAborted(bool $value = true): void {
		$this->aborted = $value;
	}

	/**
	 * @param Server $server
	 * @param mixed  $progress
	 *
	 * @return bool
	 */
	public function onProgressUpdate(Server $server, $progress): bool {
		$loader = $server->getPluginManager()->getPlugin("BlockSniper");
		if($loader instanceof Loader) {
			if($loader->isEnabled()) {
				$loader->getLogger()->debug($progress);
				return true;
			}
		}
		$this->setAborted();
		return false;
	}
}