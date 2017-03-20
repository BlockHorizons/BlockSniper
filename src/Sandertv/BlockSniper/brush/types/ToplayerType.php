<?php

namespace Sandertv\BlockSniper\brush\types;

use pocketmine\block\Block;
use pocketmine\item\Item;
use pocketmine\level\Level;
use pocketmine\math\Vector3;
use pocketmine\Player;
use Sandertv\BlockSniper\brush\BaseType;
use Sandertv\BlockSniper\brush\Brush;
use Sandertv\BlockSniper\Loader;

class ToplayerType extends BaseType {
	
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
				if($block->getSide(Block::SIDE_UP)->getId() === Item::AIR) {
					$randomBlock = Brush::$brush[$this->player->getId()]["blocks"][array_rand(Brush::$brush[$this->player->getId()]["blocks"])];
					for($y = $block->y; $y >= $block->y - Brush::getHeight($this->player); $y--) {
						$undoBlocks[] = $this->level->getBlock(new Vector3($block->x, $y, $block->z));
						$this->level->setBlock(new Vector3($block->x, $y, $block->z), $randomBlock, false, false);
					}
				}
			}
		}
		$this->getMain()->getUndoStore()->saveUndo($undoBlocks);
		return true;
	}
	
	public function getName(): string {
		return "Top Layer";
	}
	
	public function getPermission(): string {
		return "blocksniper.type.toplayer";
	}
}
