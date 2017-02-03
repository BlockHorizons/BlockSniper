<?php

namespace Sandertv\BlockSniper\brush\types;

use pocketmine\level\Level;
use pocketmine\math\Vector3;
use pocketmine\Player;
use Sandertv\BlockSniper\brush\BaseType;
use Sandertv\BlockSniper\brush\Brush;
use Sandertv\BlockSniper\Loader;

class FillType extends BaseType {
	
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
			$randomBlock = Brush::$brush[$this->player->getId()]["blocks"][array_rand(Brush::$brush[$this->player->getId()]["blocks"])];
			$undoBlocks[] = $block;
			$this->level->setBlock(new Vector3($block->x, $block->y, $block->z), $randomBlock, false, false);
		}
		$this->getMain()->getUndoStore()->saveUndo($undoBlocks);
		return true;
	}
	
	public function getName(): string {
		return "Fill";
	}
	
	public function getPermission(): string {
		return "blocksniper.type.fill";
	}
	
	public function getApproximateBlocks(): int {
		// TODO
	}
	
	public function getLevel(): Level {
		return $this->level;
	}
}
