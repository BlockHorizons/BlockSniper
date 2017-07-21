<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockSniper\cloning\types;

use BlockHorizons\BlockSniper\cloning\BaseClone;
use BlockHorizons\BlockSniper\cloning\CloneStorer;
use pocketmine\item\Item;
use pocketmine\level\Position;
use pocketmine\Player;

class TemplateType extends BaseClone {

	public function __construct(CloneStorer $cloneStorer, Player $player, bool $saveAir, Position $center, array $blocks, string $name) {
		parent::__construct($cloneStorer, $player, $saveAir, $center, $blocks, $name);
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