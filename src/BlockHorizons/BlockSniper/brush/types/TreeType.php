<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\brush\types;

use BlockHorizons\BlockSniper\brush\BaseType;
use BlockHorizons\BlockSniper\sessions\SessionManager;
use pocketmine\block\Block;
use pocketmine\level\ChunkManager;
use pocketmine\level\generator\object\Tree;
use pocketmine\Player;
use pocketmine\utils\Random;

/*
 * Grows a tree on the target block.
 * This brush can NOT undo.
 */

class TreeType extends BaseType{

	const ID = self::TYPE_TREE;

	public function __construct(Player $player, ChunkManager $level, array $blocks){
		parent::__construct($player, $level, $blocks);
		$this->center = $player->getTargetBlock(100)->asVector3();
		$this->tree = SessionManager::getPlayerSession($player)->getBrush()->tree;
	}

	/**
	 * @return Block[]
	 */
	public function fillSynchronously() : array{
		if($this->myPlotChecked){
			return [];
		}
		Tree::growTree($this->getLevel(), (int) $this->center->x, (int) $this->center->y, (int) $this->center->z, new Random(mt_rand()), $this->tree);

		return [];
	}

	/**
	 * @return string
	 */
	public function getName() : string{
		return "Tree";
	}

	/**
	 * Returns the tree ID of this type.
	 *
	 * @return int
	 */
	public function getTree() : int{
		return $this->tree;
	}

	/**
	 * @return bool
	 */
	public function canBeExecutedAsynchronously() : bool{
		return false;
	}
}