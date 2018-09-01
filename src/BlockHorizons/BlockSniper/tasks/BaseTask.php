<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\tasks;

use BlockHorizons\BlockSniper\Loader;
use pocketmine\scheduler\Task;

abstract class BaseTask extends Task{

	/** @var Loader */
	protected $loader = null;

	public function __construct(Loader $loader){
		$this->loader = $loader;
	}

	/**
	 * @return Loader
	 */
	public function getLoader() : Loader{
		return $this->loader;
	}
}