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
		$undoBlocks = [];
		foreach($this->blocks as $block) {
			if(($selectedBlock = $block->getSide(Block::SIDE_UP)->getId()) === Block::AIR) {
				$undoBlocks[] = $selectedBlock;
				$this->level->setBlock($selectedBlock, $block, false, false);
			}
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
