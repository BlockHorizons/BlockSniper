<?php

namespace BlockHorizons\BlockSniper\cloning\types;

use BlockHorizons\BlockSniper\cloning\BaseClone;
use BlockHorizons\BlockSniper\cloning\CloneStorer;
use pocketmine\item\Item;
use pocketmine\level\Position;
use pocketmine\Player;

class CopyType extends BaseClone {
	
	public function __construct(CloneStorer $cloneStorer, Player $player, bool $saveAir, Position $center, array $blocks) {
		parent::__construct($cloneStorer, $player, $saveAir, $center, $blocks);
	}
	
	public function getName(): string {
		return "Copy";
	}
	
	public function saveClone() {
		$copyBlocks = [];
		foreach($this->blocks as $block) {
			if($block->getId() === Item::AIR && $this->saveAir === false) {
				continue;
			}
			$templateBlocks[] = $block;
		}
		$this->getCloneStorer()->setOriginalCenter($this->center, $this->player);
		$this->getCloneStorer()->saveCopy($copyBlocks, $this->player);
		return true;
	}
}
