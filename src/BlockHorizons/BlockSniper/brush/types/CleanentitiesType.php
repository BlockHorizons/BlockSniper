<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockSniper\brush\types;

use BlockHorizons\BlockSniper\brush\BaseType;
use pocketmine\level\ChunkManager;
use pocketmine\math\AxisAlignedBB;
use pocketmine\Player;

class CleanentitiesType extends BaseType {

	/** @var int */
	protected $id = self::TYPE_CLEANENTITIES;

	/*
	 * Clears all entities within the brush radius. This brush can not undo.
	 */
	public function __construct(Player $player, ChunkManager $level, array $blocks) {
		parent::__construct($player, $level, $blocks);
	}

	/**
	 * @return array
	 */
	public function fillShape(): array {
		foreach($this->blocks as $block) {
			foreach($block->getLevel()->getNearbyEntities(new AxisAlignedBB($block->x, $block->y, $block->z, $block->x + 1, $block->y + 1, $block->z + 1)) as $entity) {
				if(!($entity instanceof Player)) {
					$entity->close();
				}
			}
		}
		return [];
	}

	public function getName(): string {
		return "Clean Entities";
	}

	public function canExecuteAsynchronously(): bool {
		return false;
	}

	public function fillAsynchronously(): void {
		return;
	}
}
