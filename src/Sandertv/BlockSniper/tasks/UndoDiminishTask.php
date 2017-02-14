<?php

namespace Sandertv\BlockSniper\tasks;

use Sandertv\BlockSniper\Loader;

class UndoDiminishTask extends BaseTask {
	
	public function __construct(Loader $owner) {
		parent::__construct($owner);
	}
	
	public function onRun($currentTick) {
		if($this->getUndoStore()->undoStorageExists()) {
			if($this->getUndoStore()->getLastUndoActivity() >= 180) {
				$this->getUndoStore()->unsetFirstUndo();
			}
		}
	}
}