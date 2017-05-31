<?php

namespace BlockHorizons\BlockSniper\tasks\spread;

use BlockHorizons\BlockSniper\brush\BaseShape;
use BlockHorizons\BlockSniper\brush\BaseType;
use BlockHorizons\BlockSniper\Loader;
use BlockHorizons\BlockSniper\tasks\BaseTask;
use BlockHorizons\BlockSniper\undo\Undo;

class TickSpreadBrushTask extends BaseTask {

	private $shape;
	private $type;
	private $blocksProcessed = [];

	private $workerId;

	public function __construct(Loader $loader, BaseShape $shape, BaseType $type, int $workerId) {
		parent::__construct($loader);
		$this->shape = $shape;
		$this->type = $type;
		$this->workerId = $workerId;
	}

	public function onRun($currentTick) {
		$tickProcessedBlocks = [];
		$i = 0;
		foreach($this->shape->getBlocksInside(true, $this->getLoader()->getSettings()->getBlocksPerTick()) as $block) {
			$this->blocksProcessed[] = $block;
			$tickProcessedBlocks[] = $block;
			$i++;
		}
		$this->type->setBlocksInside($tickProcessedBlocks);
		$this->type->fillShape();

		if($i < $this->getLoader()->getSettings()->getBlocksPerTick() - 1) {
			$this->getLoader()->getServer()->getScheduler()->cancelTask($this->getTaskId());
			$this->getLoader()->getUndoStorer()->saveUndo(new Undo($this->blocksProcessed), $this->shape->getPlayer());
			$this->getLoader()->getWorkerManager()->getWorker($this->workerId)->clearOccupation();
		}
	}
}