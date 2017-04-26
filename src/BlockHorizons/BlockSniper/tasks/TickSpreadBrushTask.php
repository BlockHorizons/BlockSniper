<?php

namespace BlockHorizons\BlockSniper\tasks;

use BlockHorizons\BlockSniper\brush\BaseType;
use BlockHorizons\BlockSniper\Loader;

class TickSpreadBrushTask extends BaseTask {

	private $blocksInside;
	private $type;
	private $ticks;

	public function __construct(Loader $loader, array $blocksInside, BaseType $type, int $ticks) {
		parent::__construct($loader);
		$this->blocksInside = $blocksInside;
		$this->type = $type;
		$this->ticks = $ticks;
	}

	public function onRun($currentTick) {
		$tickProcessedBlocks = [];
		if($currentTick <= $this->ticks) {
			$i = 0;
			foreach($this->blocksInside as $key => $block) {
				$i++;
				$tickProcessedBlocks[] = $block;
				unset($this->blocksInside[$key]);
				if($i === $this->getLoader()->getSettings()->get("Blocks-Per-Tick")) {
					break;
				}
			}
			$this->type->setBlocksInside($tickProcessedBlocks);
			$this->type->fillShape();
		} else {
			$this->getLoader()->getServer()->getScheduler()->cancelTask($this->getTaskId());
		}
		$this->ticks++;
	}
}