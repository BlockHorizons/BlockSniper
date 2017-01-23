<?php

namespace Sandertv\BlockSniper\brush\types;

use pocketmine\block\Block;
use pocketmine\item\Item;
use pocketmine\level\Level;
use pocketmine\math\Vector3;
use Sandertv\BlockSniper\brush\BaseType;
use Sandertv\BlockSniper\Loader;
use Sandertv\BlockSniper\brush\Brush;
use pocketmine\Player;

class ReplaceType extends BaseType {
	
	public $player;
	public $level;
	public $blocks;
	
	public function __construct(Loader $main, Player $player, Level $level, array $blocks) {
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
			$obsolete = Brush::getObsolete($this->player);
			$randomBlock = Brush::$brush[$this->player->getId()]["blocks"][array_rand(Brush::$brush[$this->player->getId()]["blocks"])];
			if($block->getId() === $obsolete->getId()) {
				if($block->getId() !== $randomBlock->getId()) {
					$undoBlocks[] = $block;
				}
				$this->level->setBlock(new Vector3($block->x, $block->y, $block->z), $randomBlock, false, false);
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
	
	public function getApproximateBlocks(): int {
		// TODO
	}
	
	public function getLevel(): Level {
		return $this->level;
	}
}

