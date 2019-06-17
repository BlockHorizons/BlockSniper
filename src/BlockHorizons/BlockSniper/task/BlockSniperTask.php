<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\task;

use BlockHorizons\BlockSniper\Loader;
use pocketmine\scheduler\Task;

abstract class BlockSniperTask extends Task{

	/** @var Loader */
	protected $loader = null;

	public function __construct(Loader $loader){
		$this->loader = $loader;
	}
}