<?php

namespace Sandertv\BlockSniper\brush\types;

use pocketmine\level\Level;
use pocketmine\math\Vector3;
use pocketmine\Player;
use Sandertv\BlockSniper\brush\BaseType;
use Sandertv\BlockSniper\brush\Brush;
use Sandertv\BlockSniper\Loader;

class LayerType extends BaseType {
	
	public $player;
	public $level;
	public $center;
	public $blocks;
	
	public function __construct(Loader $main, Player $player, Level $level, array $blocks = []) {
		parent::__construct($main);
		$this->level = $level;
		$this->center = $player->getTargetBlock(100);
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
			if($block->getId() !== $randomBlock->getId()) {
				$undoBlocks[] = $block;
			}
			$this->level->setBlock(new Vector3($block->x, $this->center->y + 1, $block->z), $randomBlock, false, false);
		}
		$this->getMain()->getUndoStore()->saveUndo($undoBlocks);
		return true;
	}
	
	public function getName(): string {
		return "Layer";
	}
	
	public function getPermission(): string {
		return "blocksniper.type.layer";
	}
	
	public function getApproximateBlocks(): int {
		// TODO
	}
	
	public function getLevel(): Level {
		return $this->level;
	}
}

