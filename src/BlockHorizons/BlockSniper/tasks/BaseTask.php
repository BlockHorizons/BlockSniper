<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockSniper\tasks;

use BlockHorizons\BlockSniper\Loader;
use BlockHorizons\BlockSniper\undo\RevertStorer;
use BlockHorizons\BlockSniper\undo\UndoStorer;
use pocketmine\scheduler\PluginTask;

abstract class BaseTask extends PluginTask {

	protected $loader;

	public function __construct(Loader $loader) {
		parent::__construct($loader);
		$this->loader = $loader;
	}

	/**
	 * @return RevertStorer
	 */
	public function getRevertStorer(): RevertStorer {
		return $this->getLoader()->getRevertStorer();
	}

	/**
	 * @return Loader
	 */
	public function getLoader(): Loader {
		return $this->loader;
	}
}