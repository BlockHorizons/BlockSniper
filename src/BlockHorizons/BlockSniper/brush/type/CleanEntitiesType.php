<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\brush\type;

use BlockHorizons\BlockSniper\brush\Type;
use Generator;
use pocketmine\block\Block;
use pocketmine\entity\Entity;
use pocketmine\math\AxisAlignedBB;
use pocketmine\player\Player;

/*
 * Clears all entities within the brush radius. This brush can not undo.
 */

class CleanEntitiesType extends Type{

	protected function fill() : Generator{
		foreach($this->mustGetBlocks() as $block){
			/** @var Entity $entity */
			foreach($block->getPosition()->getWorld()->getNearbyEntities(new AxisAlignedBB($block->getPosition()->x, $block->getPosition()->y, $block->getPosition()->z, $block->getPosition()->x + 1, $block->getPosition()->y + 1, $block->getPosition()->z + 1)) as $entity){
				if(!($entity instanceof Player)){
					$entity->flagForDespawn();
				}
			}
		}
		// Make PHP recognize this is a generator.
		yield from [];
	}

	/**
	 * @return bool
	 */
	public function canBeExecutedAsynchronously() : bool{
		return false;
	}

	/**
	 * @return bool
	 */
	public function usesBrushBlocks() : bool{
		return false;
	}

	/**
	 * @return string
	 */
	public function getName() : string{
		return "Clean Entities";
	}
}
