<?php

namespace Sandertv\BlockSniper\brush\types;

use pocketmine\level\Level;
use pocketmine\math\Vector3;
use pocketmine\Player;
use Sandertv\BlockSniper\brush\BaseType;
use Sandertv\BlockSniper\brush\Brush;
use Sandertv\BlockSniper\Loader;

class ReplaceType extends BaseType {
	
	public function __construct(Loader $main, Player $player, Level $level, array $blocks) {
		parent::__construct($main);
		$this->level = $level;
		$this->player = $player;
		$this->blocks = $blocks;
		$this->obsolete = Brush::getObsolete($this->player);
	}
	
	/**
	 * @return bool
	 */
	public function fillShape(): bool {
		$undoBlocks = [];
		foreach($this->blocks as $block) {
			$randomBlock = Brush::$brush[$this->player->getId()]["blocks"][array_rand(Brush::$brush[$this->player->getId()]["blocks"])];
			foreach($this->obsolete as $obsolete) {
				if($block->getId() === $obsolete->getId()) {
					if($block->getId() !== $randomBlock->getId()) {
						$undoBlocks[] = $block;
					}
					$this->level->setBlock(new Vector3($block->x, $block->y, $block->z), $randomBlock, false, false);
				}
			}
		}
		$this->getMain()->getUndoStore()->saveUndo($undoBlocks);
		return true;
	}
	
	public function getName(): string {
		return "Replace";
	}
	
	public function getPermission(): string {
		return "blocksniper.type.replace";
	}
	
	public function getLevel(): Level {
		return $this->level;
	}
}

