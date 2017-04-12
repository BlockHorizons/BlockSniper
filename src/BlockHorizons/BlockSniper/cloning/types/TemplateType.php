<?php

namespace BlockHorizons\BlockSniper\cloning\types;

use BlockHorizons\BlockSniper\cloning\BaseClone;
use BlockHorizons\BlockSniper\cloning\CloneStorer;
use pocketmine\item\Item;
use pocketmine\level\Level;
use pocketmine\level\Position;

class TemplateType extends BaseClone {

	public function __construct(CloneStorer $cloneStorer, Level $level, bool $saveAir, Position $center, array $blocks, string $name) {
		parent::__construct($cloneStorer, $level, $saveAir, $center, $blocks, $name);
	}

	public function getName(): string {
		return "Template";
	}

	public function saveClone() {
		$templateBlocks = [];
		foreach($this->blocks as $block) {
			if($block->getId() === Item::AIR && $this->saveAir === false) {
				continue;
			}
			$templateBlocks[] = $block;
		}
		$this->getCloneStorer()->saveTemplate($this->name, $templateBlocks, $this->center);
		return true;
	}
}