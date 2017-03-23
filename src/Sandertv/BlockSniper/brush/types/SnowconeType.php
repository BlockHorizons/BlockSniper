<?php

namespace Sandertv\BlockSniper\brush\types;

use pocketmine\block\Block;
use pocketmine\level\Level;
use pocketmine\Player;
use Sandertv\BlockSniper\brush\BaseType;
use Sandertv\BlockSniper\Loader;

class SnowconeType extends BaseType {
	
	/*
	 * Lays a layer of snow on top of the terrain, and raises it if there is snow already.
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
			if($block->getId() !== Block::AIR && ($block->getId() !== Block::SNOW_LAYER || ($block->getDamage() % 7 === 0 && $block->getId() === Block::SNOW_LAYER))) {
				$topBlock = $block->getSide(Block::SIDE_UP);
				if($topBlock->getId() === Block::AIR || $topBlock->getId() === Block::SNOW_LAYER) {
					if($topBlock->getDamage() < 7) {
						$undoBlocks[] = $topBlock;
						$this->level->setBlockDataAt($topBlock->x, $topBlock->y, $topBlock->z, $topBlock->getDamage() + 1);
					} else {
						$undoBlocks[] = $block->getSide(Block::SIDE_UP);
						$this->level->setBlock($block->getSide(Block::SIDE_UP), Block::get(Block::SNOW_LAYER), false, false);
					}
				}
			}
		}
		$this->getMain()->getUndoStore()->saveUndo($undoBlocks);
		return true;
	}
	
	public function getName(): string {
		return "Snow Cone";
	}
	
	public function getPermission(): string {
		return "blocksniper.type.snowcone";
	}
}
