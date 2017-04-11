<?php

namespace BlockHorizons\BlockSniper\brush\types;

use BlockHorizons\BlockSniper\brush\BaseType;
use BlockHorizons\BlockSniper\undo\UndoStorer;
use pocketmine\level\Level;
use pocketmine\Player;

class CleanentitiesType extends BaseType {
	
	/*
	 * Clears all entities within the brush radius.
	 */
	public function __construct(UndoStorer $undoStorer, Player $player, Level $level, array $blocks) {
		parent::__construct($undoStorer, $player, $level, $blocks);
	}
	
	/**
	 * @return bool
	 */
	public function fillShape(): bool {
		foreach($this->blocks as $block) {
			foreach($this->level->getEntities() as $entity) {
				if($entity->distanceSquared($block) <= 1 && !($entity instanceof Player)) {
					$entity->close();
				}
			}
		}
		return true;
	}
	
	public function getName(): string {
		return "Clean Entities";
	}
}
