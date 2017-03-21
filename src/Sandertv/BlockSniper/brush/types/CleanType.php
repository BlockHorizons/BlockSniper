<?php

namespace Sandertv\BlockSniper\brush\types;

use pocketmine\block\Block;
use pocketmine\level\Level;
use pocketmine\math\Vector3;
use pocketmine\Player;
use Sandertv\BlockSniper\brush\BaseType;
use Sandertv\BlockSniper\Loader;

class CleanType extends BaseType {
	
	/*
	 * Removes all non-natural blocks within the brush radius.
	 */
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
		$undoBlocks = [];
		foreach($this->blocks as $block) {
			$blockId = $block->getId();
			if($blockId !== Block::AIR && $blockId !== Block::STONE && $blockId !== Block::GRASS && $blockId !== Block::DIRT && $blockId !== Block::GRAVEL && $blockId !== Block::SAND && $blockId !== Block::SANDSTONE) {
				if($blockId !== Block::AIR) {
					$undoBlocks[] = $block;
				}
				$this->level->setBlock(new Vector3($block->x, $block->y, $block->z), Block::get(Block::AIR), false, false);
			}
		}
		$this->getMain()->getUndoStore()->saveUndo($undoBlocks);
		return true;
	}
	
	public function getName(): string {
		return "Clean";
	}
	
	public function getPermission(): string {
		return "blocksniper.type.clean";
	}
}
