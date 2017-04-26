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

	/**
	 * @param Loader    $loader
	 * @param Undo|Redo $undo
	 * @param Player    $player
	 * @param int       $ticks
	 */
	public function __construct(Loader $loader, $undo, Player $player, int $ticks) {
		parent::__construct($loader);
		$this->undo = $undo;
		$this->player = $player;
		$this->ticks = $ticks;
	}

	public function onRun($currentTick) {
		$blocksInside = $this->undo->getBlocks();

		if($this->actualTick <= $this->ticks) {
			$i = 0;
			foreach($blocksInside as $key => $block) {
				if($block->getId() === $this->player->getLevel()->getBlock($block)) {
					continue;
				}
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