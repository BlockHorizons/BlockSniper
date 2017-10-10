<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockSniper\brush\types;

use BlockHorizons\BlockSniper\brush\BaseType;
use BlockHorizons\BlockSniper\sessions\SessionManager;
use pocketmine\block\Block;
use pocketmine\block\Flowable;
use pocketmine\level\ChunkManager;
use pocketmine\level\generator\object\Tree;
use pocketmine\math\Vector3;
use pocketmine\Player;
use pocketmine\utils\Random;

class TreeType extends BaseType {

	/** @var int */
	protected $id = self::TYPE_TREE;
	/** @var Vector3 */
	protected $center = null;

	/*
	 * Grows a tree on the target block. This brush can not undo.
	 */
	public function __construct(Player $player, ChunkManager $level, array $blocks) {
		parent::__construct($player, $level, $blocks);
		$this->center = $player->getTargetBlock(100)->asVector3();
		$this->tree = SessionManager::getPlayerSession($player)->getBrush()->getTreeType();
	}

	/**
	 * @return Block[]
	 */
	public function fillSynchronously(): array {
		if(!($this->getLevel()->getBlock($this->center) instanceof Flowable) && $this->getLevel()->getBlock($this->center)->getId() !== Block::AIR) {
			$this->center->y++;
		}
		Tree::growTree($this->getLevel(), $this->center->x, $this->center->y + 1, $this->center->z, new Random(mt_rand()), $this->tree);
		return [];
	}

	public function getName(): string {
		return "Tree";
	}

	/**
	 * Returns the tree ID of this type.
	 *
	 * @return int
	 */
	public function getTree(): int {
		return $this->tree;
	}

	/**
	 * @return bool
	 */
	public function canBeExecutedAsynchronously(): bool {
		return false;
	}
}