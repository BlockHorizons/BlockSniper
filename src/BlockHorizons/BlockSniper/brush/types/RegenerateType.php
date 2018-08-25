<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\brush\types;

use BlockHorizons\BlockSniper\brush\BaseType;
use pocketmine\block\Block;
use pocketmine\level\ChunkManager;
use pocketmine\math\Vector3;
use pocketmine\Player;

/*
 * Regenerates the chunk looked at.
 * This brush can NOT undo.
 */

class RegenerateType extends BaseType{

	const ID = self::TYPE_REGENERATE;

	public function __construct(Player $player, ChunkManager $manager, array $blocks){
		parent::__construct($player, $manager, $blocks);
		$this->center = $player->getTargetBlock(100)->asVector3();
	}

	/**
	 * @return Block[]
	 */
	public function fillSynchronously() : array{
		if($this->myPlotChecked){
			return [];
		}
		$x = $this->center->x >> 4;
		$z = $this->center->z >> 4;
		$this->getLevel()->getChunk($x, $z)->setPopulated(false);
		$this->getLevel()->getChunk($x, $z)->setGenerated(false);

		return [];
	}

	public function canBeExecutedAsynchronously() : bool{
		return false;
	}

	public function getName() : string{
		return "Chunk Regenerate";
	}

	/**
	 * Returns the center of this type.
	 *
	 * @return Vector3
	 */
	public function getCenter() : Vector3{
		return $this->center;
	}
}