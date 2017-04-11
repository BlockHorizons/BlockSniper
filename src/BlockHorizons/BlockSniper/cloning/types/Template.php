<?php

namespace BlockHorizons\BlockSniper\cloning\types;

use BlockHorizons\BlockSniper\cloning\BaseClone;
use BlockHorizons\BlockSniper\cloning\CloneStorer;
use pocketmine\item\Item;
use pocketmine\level\Level;
use pocketmine\level\Position;

class Template extends BaseClone {
	
	public function __construct(CloneStorer $cloneStorer, Level $level, Position $center, array $blocks, string $name) {
		parent::__construct($cloneStorer, $level, $center, $blocks, $name);
	}
	
	public function getName(): string {
		return "Template";
	}
	
	public function saveClone() {
		$templateBlocks = [];
		foreach($this->blocks as $block) {
			if($block->getId() !== Item::AIR) {
				$templateBlocks[] = $block;
			}
		}
		$this->getCloneStorer()->saveTemplate($this->name, $templateBlocks, $this->center);
		return true;
	}
}