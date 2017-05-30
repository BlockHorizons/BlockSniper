<?php

namespace BlockHorizons\BlockSniper\tasks;

use BlockHorizons\BlockSniper\brush\BaseShape;
use BlockHorizons\BlockSniper\brush\BaseType;
use BlockHorizons\BlockSniper\Loader;
use BlockHorizons\BlockSniper\undo\Undo;

class TickSpreadBrushTask extends BaseTask {

	private $shape;
	private $type;
	private $blocksProcessed = [];

	public function __construct(Loader $loader, BaseShape $shape, BaseType $type) {
		parent::__construct($loader);
		$this->shape = $shape;
		$this->type = $type;
	}

	public function onRun($currentTick) {
		$tickProcessedBlocks = [];
		$i = 0;
		foreach($this->shape->getBlocksInside(true, $this->getLoader()->getSettings()->getBlocksPerTick()) as $key => $block) {
			$this->blocksProcessed[] = $block;
			$tickProcessedBlocks[] = $block;
			$i++;
		}
		if($i > $this->getLoader()->getSettings()->getBlocksPerTick()) {
			$this->getLoader()->getServer()->getScheduler()->cancelTask($this->getTaskId());
			$this->getLoader()->getUndoStorer()->saveUndo(new Undo($this->blocksProcessed), $this->shape->getPlayer());
		}
		$this->type->setBlocksInside($tickProcessedBlocks);
		$this->type->fillShape();
	}
}