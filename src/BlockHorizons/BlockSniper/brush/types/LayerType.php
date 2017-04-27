<?php

namespace BlockHorizons\BlockSniper\brush\types;

use BlockHorizons\BlockSniper\brush\BaseType;
use BlockHorizons\BlockSniper\brush\BrushManager;
use pocketmine\level\Level;
use pocketmine\math\Vector3;
use pocketmine\Player;

class LayerType extends BaseType {
	
	/*
	 * Lays a thin layer of blocks within the brush radius.
	 */
	public function __construct(Player $player, Level $level, array $blocks) {
		parent::__construct($player, $level, $blocks);
		$this->center = $player->getTargetBlock(100);
	}

	/**
	 * @return array
	 */
	public function fillShape(): array {
		$undoBlocks = [];
		foreach($this->blocks as $block) {
			$randomBlock = BrushManager::get($this->player)->getBlocks()[array_rand(BrushManager::get($this->player)->getBlocks())];
			if($block->getId() !== $randomBlock->getId()) {
				$undoBlocks[] = $block;
			}
			$this->level->setBlock(new Vector3($block->x, $this->center->y + 1, $block->z), $randomBlock, false, false);
		}
		return $undoBlocks;
	}
	
	public function getName(): string {
		return "Layer";
	}
}

