<?php

namespace BlockHorizons\BlockSniper\cloning\types;

use BlockHorizons\BlockSniper\cloning\BaseClone;
use BlockHorizons\BlockSniper\cloning\CloneStorer;
use pocketmine\item\Item;
use pocketmine\level\Position;
use pocketmine\Player;

class SchematicType extends BaseClone {

	public function __construct(CloneStorer $cloneStorer, Player $player, bool $saveAir, Position $center, array $blocks, string $name) {
		parent::__construct($cloneStorer, $player, $saveAir, $center, $blocks, $name);
	}

	public function getName(): string {
		return "Schematic";
	}

	public function saveClone() {
		$schematicBlocks = [];
		foreach($this->blocks as $block) {
			if($block->getId() === Item::AIR && $this->saveAir === false) {
				continue;
			}
			$schematicBlocks[] = $block;
		}
		$this->getCloneStorer()->getLoader()->getSchematicProcessor()->save($this->name);
		return true;
	}
}