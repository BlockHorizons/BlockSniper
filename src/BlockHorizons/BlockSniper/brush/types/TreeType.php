<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\brush\types;

use BlockHorizons\BlockSniper\brush\BaseType;
use BlockHorizons\BlockSniper\brush\Brush;
use BlockHorizons\BlockSniper\brush\TreeProperties;
use BlockHorizons\BlockSniper\sessions\SessionManager;
use pocketmine\level\ChunkManager;
use pocketmine\Player;

/*
 * Grows a custom tree on the target block.
 */

class TreeType extends BaseType{

	const ID = self::TYPE_TREE;

	/** @var Brush */
	private $brush;

	public function __construct(Player $player, ChunkManager $level){
		parent::__construct($player, $level);
		$this->center = $player->getTargetBlock(100)->asPosition();
		$this->brush = SessionManager::getPlayerSession($player)->getBrush();
	}

	/**
	 * @return \Generator
	 */
	public function fillSynchronously() : \Generator{
		if(false){
			yield;
		}
		$tree = new Tree($this->center, $this->brush, $this);
		foreach($tree->build() as $block){
			yield $block;
		}
	}

	/**
	 * @return string
	 */
	public function getName() : string{
		return "Tree";
	}

	/**
	 * Returns the tree properties of this type.
	 *
	 * @return TreeProperties
	 */
	public function getTree() : TreeProperties{
		return $this->brush->tree;
	}

	/**
	 * @return bool
	 */
	public function canBeExecutedAsynchronously() : bool{
		return false;
	}
}