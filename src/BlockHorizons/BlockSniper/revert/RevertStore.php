<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\revert;

use function array_keys;
use function count;
use function max;
use function min;

class RevertStore{

	/** @var Revert[] */
	private $undoStack = [];
	/** @var Revert[] */
	private $redoStack = [];

	/** @var int */
	private $maxRevertStores;

	public function __construct(int $maxRevertStores){
		$this->maxRevertStores = $maxRevertStores;
	}

	/**
	 * @param int $amount
	 */
	public function restoreLatestUndo(int $amount = 1) : void{
		for($i = 0; $i < $amount; $i++){
			if(empty($this->undoStack)){
				return;
			}
			$key = max(array_keys($this->undoStack));
			$revert = $this->undoStack[$key];
			$this->saveRedo($revert->restore());

			unset($this->undoStack[$key]);
		}
	}

	/**
	 * @param int $amount
	 */
	public function restoreLatestRedo(int $amount = 1) : void{
		for($i = 0; $i < $amount; $i++){
			if(empty($this->redoStack)){
				return;
			}
			$key = max(array_keys($this->redoStack));
			$revert = $this->redoStack[$key];
			$this->saveUndo($revert->restore());

			unset($this->redoStack[$key]);
		}
	}

	/**
	 * @param Revert $revert
	 */
	public function saveRedo(Revert $revert) : void{
		if(count($this->redoStack) === $this->maxRevertStores){
			if(!empty($this->redoStack)){
				unset($this->redoStack[min(array_keys($this->redoStack))]);
			}
		}
		$this->redoStack[] = $revert;
	}

	/**
	 * @param Revert $revert
	 */
	public function saveUndo(Revert $revert) : void{
		if(count($this->undoStack) === $this->maxRevertStores){
			if(!empty($this->undoStack)){
				unset($this->undoStack[min(array_keys($this->undoStack))]);
			}
		}
		$this->undoStack[] = $revert;
	}

	/**
	 * @return int
	 */
	public function getUndoCount() : int{
		return count($this->undoStack);
	}

	/**
	 * @return int
	 */
	public function getRedoCount() : int{
		return count($this->redoStack);
	}
}
