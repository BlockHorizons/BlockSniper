<?php

namespace Sandertv\BlockSniper\brush\types;

use pocketmine\level\Level;
use pocketmine\Player;
use pocketmine\block\Block;
use Sandertv\BlockSniper\brush\BaseType;
use Sandertv\BlockSniper\Loader;

class RaiseType extends BaseType {
	
	public $level;
	public $blocks;
	public $player;
	
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
		$savedBlocks = [];
		$holeBlocks = [];
		$undoBlocks = [];
		$sides = [
			Block::SIDE_NORTH,
			Block::SIDE_EAST,
			Block::SIDE_SOUTH,
			Block::SIDE_WEST
		];
		foreach($this->blocks as $block) {
			$valid = 0;
			foreach($sides as $side) {
				if($block->getSide($side)->getId() !== Block::AIR) {
					$valid++;
				}
			}
			if($valid <= 3) {
				$holeBlocks[] = $block;
			}
		}
		foreach($holeBlocks as $selectedBlock) {
			$undoBlocks[] = $selectedBlock;
			$this->level->setBlock($selectedBlock, $this->level->getBlock($selectedBlock->subtract(0, 1)), false, false);
		}
		foreach($this->blocks as $block) {
			if($block->getSide(Block::SIDE_UP)->getId() === Block::AIR && $block->getId() !== Block::AIR) {
				$savedBlocks[] = $block;
			}
		}
		foreach($savedBlocks as $selectedBlock) {
			$undoBlocks[] = $selectedBlock->getSide(Block::SIDE_UP);
			$this->level->setBlock($selectedBlock->getSide(Block::SIDE_UP), $selectedBlock, false, false);
		}
		$this->getMain()->getUndoStore()->saveUndo($undoBlocks);
		return true;
	}
	
	public function getName(): string {
		return "Raise";
	}
	
	public function getPermission(): string {
		return "blocksniper.type.raise";
	}
	
	public function getApproximateBlocks(): int {
		// TODO
	}
	
	public function getLevel(): Level {
		return $this->level;
	}
}
