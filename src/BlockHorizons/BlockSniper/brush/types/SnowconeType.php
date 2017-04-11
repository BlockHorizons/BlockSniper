<?php

namespace BlockHorizons\BlockSniper\brush\types;

use BlockHorizons\BlockSniper\brush\BaseType;
use BlockHorizons\BlockSniper\undo\UndoStorer;
use pocketmine\block\Block;
use pocketmine\level\Level;
use pocketmine\Player;

class SnowconeType extends BaseType {
	
	/*
	 * Lays a layer of snow on top of the terrain, and raises it if there is snow already.
	 */
	public function __construct(UndoStorer $undoStorer, Player $player, Level $level, array $blocks) {
		parent::__construct($undoStorer, $player, $level, $blocks);
	}
	
	/**
	 * @return bool
	 */
	public function fillShape(): bool {
		$undoBlocks = [];
		foreach($this->blocks as $block) {
			if($block->getId() !== Block::AIR && $block->getId() !== Block::SNOW_LAYER) {
				$topBlock = $block->getSide(Block::SIDE_UP);
				if($topBlock->getId() === Block::AIR || $topBlock->getId() === Block::SNOW_LAYER) {
					if($topBlock->getDamage() < 7 && $topBlock->getId() === Block::SNOW_LAYER) {
						$undoBlocks[] = $topBlock;
						$this->level->setBlockDataAt($topBlock->x, $topBlock->y, $topBlock->z, $topBlock->getDamage() + 1);
					} elseif($topBlock->getId() !== Block::SNOW_LAYER) {
						$undoBlocks[] = $block->getSide(Block::SIDE_UP);
						$this->level->setBlock($block->getSide(Block::SIDE_UP), Block::get(Block::SNOW_LAYER), false, false);
					}
				}
			}
		}
		$this->getUndoStore()->saveUndo($undoBlocks, $this->player);
		return true;
	}
	
	public function getName(): string {
		return "Snow Cone";
	}
	
	public function getPermission(): string {
		return "blocksniper.type.snowcone";
	}
}
