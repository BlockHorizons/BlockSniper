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

	/**
	 * @return Generator
	 */
	protected function fill() : Generator{
		foreach($this->blocks as $block){
			/** @var Entity $entity */
			foreach($block->getPos()->getWorld()->getNearbyEntities(new AxisAlignedBB($block->getPos()->x, $block->getPos()->y, $block->getPos()->z, $block->getPos()->x + 1, $block->getPos()->y + 1, $block->getPos()->z + 1)) as $entity){
				if(!($entity instanceof Player)){
					$entity->flagForDespawn();
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
