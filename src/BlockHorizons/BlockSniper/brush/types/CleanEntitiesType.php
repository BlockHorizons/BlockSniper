<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\brush\types;

use BlockHorizons\BlockSniper\brush\BaseType;
use pocketmine\math\AxisAlignedBB;
use pocketmine\Player;

/*
 * Clears all entities within the brush radius. This brush can not undo.
 */

class CleanEntitiesType extends BaseType{

	public const ID = self::TYPE_CLEAN_ENTITIES;

	/**
	 * @return \Generator
	 */
	protected function fill() : \Generator{
		foreach($this->blocks as $block){
			foreach($block->getLevel()->getNearbyEntities(new AxisAlignedBB($block->x, $block->y, $block->z, $block->x + 1, $block->y + 1, $block->z + 1)) as $entity){
				if(!($entity instanceof Player)){
					$entity->close();
				}
			}
		}
		if(false){
			// Make PHP recognize this is a generator.
			yield;
		}
	}

	/**
	 * @return bool
	 */
	public function canBeExecutedAsynchronously() : bool{
		return false;
	}

	/**
	 * @return string
	 */
	public function getName() : string{
		return "Clean Entities";
	}
}
