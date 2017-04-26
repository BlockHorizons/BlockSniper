<?php

namespace BlockHorizons\BlockSniper\tasks;

use BlockHorizons\BlockSniper\Loader;
use BlockHorizons\BlockSniper\undo\Redo;
use BlockHorizons\BlockSniper\undo\Undo;
use pocketmine\Player;

class TickSpreadUndoTask extends BaseTask {

	private $undo;
	private $player;
	private $ticks;
	private $actualTick = 1;

	public function __construct(Loader $loader, $undo, Player $player, int $ticks) {
		parent::__construct($loader);
		$this->undo = $undo;
		$this->player = $player;
		$this->ticks = $ticks;
	}

	public function onRun($currentTick) {
		if($this->undo instanceof Undo) {
			$blocksInside = $this->undo->getBlocks();
			$this->getLoader()->getUndoStorer()->saveRedo($this->undo->getDetachedRedo(), $this->player);
		} elseif($this->undo instanceof Redo) {
			$blocksInside = $this->undo->getBlocks();
			$this->getLoader()->getUndoStorer()->saveUndo($this->undo->getDetachedUndoBlocks(), $this->player);
		} else {
			return;
		}

		if($this->actualTick <= $this->ticks) {
			$i = 0;
			foreach($blocksInside as $key => $block) {
				$i++;
				$this->player->getLevel()->setBlock($block, $block, false, false);
				unset($blocksInside[$key]);
				if($i === $this->getLoader()->getSettings()->get("Blocks-Per-Tick")) {
					break;
				}
			}
		} else {
			$this->getLoader()->getServer()->getScheduler()->cancelTask($this->getTaskId());
		}
		$this->actualTick++;
	}
}