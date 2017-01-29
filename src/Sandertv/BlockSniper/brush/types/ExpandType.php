<?php

namespace Sandertv\BlockSniper\brush\types;

use pocketmine\block\Block;
use pocketmine\item\Item;
use pocketmine\level\Level;
use pocketmine\Player;
use Sandertv\BlockSniper\brush\BaseType;
use Sandertv\BlockSniper\Loader;

class ExpandType extends BaseType {
	
	public $player;
	public $level;
	public $blocks;
	
	public function __construct(Loader $main, Player $player, Level $level, array $blocks = []) {
		parent::__construct($main);
		$this->level = $level;
		$this->player = $player;
		$this->blocks = $blocks;
	}
	
	/**
	 * @return bool
	 */
	public function fillShape(): bool {
		$undoBlocks = [];
		foreach($this->blocks as $block) {
			if($block->getId() !== Item::AIR) {
				$directions = [
					$block->getSide(Block::SIDE_NORTH),
					$block->getSide(Block::SIDE_SOUTH),
					$block->getSide(Block::SIDE_WEST),
					$block->getSide(Block::SIDE_EAST)
				];
				
				foreach($directions as $direction) {
					if($this->level->getBlock($direction)->getId() === Item::AIR) {
						$undoBlocks[] = $direction;
						$this->level->setBlock($direction, $block->getSide(Block::SIDE_DOWN), false, false);
					}
				}
			}
		}
		$this->getMain()->getUndoStore()->saveUndo($undoBlocks);
		return true;
	}
	
	public function getName(): string {
		return "Expand";
	}
	
	public function getPermission(): string {
		return "blocksniper.type.expand";
	}
	
	public function getApproximateBlocks(): int {
		// TODO
	}
	
	public function getLevel(): Level {
		return $this->level;
	}
}