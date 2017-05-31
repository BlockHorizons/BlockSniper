<?php

namespace BlockHorizons\BlockSniper\tasks\spread;

use BlockHorizons\BlockSniper\events\OperationFinishEvent;
use BlockHorizons\BlockSniper\Loader;
use BlockHorizons\BlockSniper\tasks\BaseTask;
use pocketmine\block\Block;
use pocketmine\Player;

class TickSpreadUndoTask extends BaseTask {

	private $undoBlocks;
	private $player;
	private $ticks;
	private $actualTick = 1;
	private $workerId;

	/**
	 * @param Loader  $loader
	 * @param Block[] $undoBlocks
	 * @param Player  $player
	 * @param int     $ticks
	 * @param int     $workerId
	 */
	public function __construct(Loader $loader, array $undoBlocks, Player $player, int $ticks, int $workerId) {
		parent::__construct($loader);
		$this->undoBlocks = $undoBlocks;
		$this->player = $player;
		$this->ticks = $ticks;
		$this->workerId = $workerId;
	}

	public function onRun($currentTick) {
		if(!$this->getLoader()->getWorkerManager()->getWorker($this->workerId)->isOccupied()) {
			$this->getLoader()->getServer()->getScheduler()->cancelTask($this->getTaskId());
		}
		if($this->actualTick <= $this->ticks) {
			$i = 0;
			foreach($this->undoBlocks as $key => $block) {
				if($block->getId() === ($previousBlock = $this->player->getLevel()->getBlock($block))->getId() && $block->getDamage() === $previousBlock->getDamage()) {
					continue;
				}
				$i++;
				$this->player->getLevel()->setBlock($block, $block, false, false);
				unset($this->undoBlocks[$key]);
				if($i === $this->getLoader()->getSettings()->getBlocksPerTick()) {
					break;
				}
			}
		} else {
			$this->getLoader()->getServer()->getScheduler()->cancelTask($this->getTaskId());
			$this->getLoader()->getWorkerManager()->getWorker($this->workerId)->clearOccupation();
		}
		$this->actualTick++;
	}
}