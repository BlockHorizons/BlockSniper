<?php

namespace Sandertv\BlockSniper\brush\types;

use pocketmine\level\Level;
use pocketmine\Player;
use Sandertv\BlockSniper\brush\BaseType;
use Sandertv\BlockSniper\Loader;

class CleanentitiesType extends BaseType {
	
	public function __construct(Loader $main, Player $player, Level $level, array $blocks) {
		parent::__construct($main);
		$this->level = $level;
		$this->blocks = $blocks;
		$this->player = $player;
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
	
	public function getPermission(): string {
		return "blocksniper.type.cleanentities";
	}
	
	public function getLevel(): Level {
		return $this->level;
	}
}
