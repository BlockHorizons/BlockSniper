<?php

namespace Sandertv\BlockSniper\brush\types;

use pocketmine\item\Item;
use pocketmine\level\Level;
use pocketmine\math\Vector3;
use pocketmine\Player;
use Sandertv\BlockSniper\brush\BaseType;
use Sandertv\BlockSniper\brush\Brush;
use Sandertv\BlockSniper\Loader;

class FlattenType extends BaseType {
	
	public $level;
	public $blocks;
	public $player;
	public $center;
	
	public function __construct(Loader $main, Player $player, Level $level, array $blocks = []) {
		parent::__construct($main);
		$this->level = $level;
		$this->blocks = $blocks;
		$this->center = $player->getTargetBlock(100);
		
		$this->player = $player;
	}
	
	/**
	 * @return bool
	 */
	public function fillShape(): bool {
		$undoBlocks = [];
		foreach($this->blocks as $block) {
			$randomBlock = Brush::$brush[$this->player->getId()]["blocks"][array_rand(Brush::$brush[$this->player->getId()]["blocks"])];
			if($block->getId() === Item::AIR && $block->y <= $this->center->y) {
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
		return "Flatten";
	}
	
	public function getPermission(): string {
		return "blocksniper.type.flatten";
	}
	
	public function getLevel(): Level {
		return $this->level;
	}
}
