<?php

namespace Sandertv\BlockSniper\brush\types;

use pocketmine\block\Block;
use pocketmine\block\Flowable;
use pocketmine\item\Item;
use pocketmine\level\Level;
use pocketmine\math\Vector3;
use pocketmine\Player;
use Sandertv\BlockSniper\brush\BaseType;
use Sandertv\BlockSniper\brush\BrushManager;
use Sandertv\BlockSniper\Loader;
use Sandertv\BlockSniper\undo\UndoStorer;

class ToplayerType extends BaseType {
	
	/*
	 * Replaces the top layer of the terrain, thickness depending on brush height, within the brush radius.
	 */
	public function __construct(UndoStorer $undoStorer, Player $player, Level $level, array $blocks) {
		parent::__construct($undoStorer, $player, $level, $blocks);
	}
	
	/**
	 * @return bool
	 */
	public function fillShape(): bool {
		$undoBlocks = [];
		foreach($this->blocks as $block) {
			if($block->getId() !== Item::AIR && !$block instanceof Flowable) {
				if($block->getSide(Block::SIDE_UP)->getId() === Item::AIR || $block->getSide(Block::SIDE_UP) instanceof Flowable) {
					$randomBlock = BrushManager::get($this->player)->getBlocks()[array_rand(BrushManager::get($this->player)->getBlocks())];
					for($y = $block->y; $y >= $block->y - BrushManager::get($this->player)->getHeight(); $y--) {
						$undoBlocks[] = $this->level->getBlock(new Vector3($block->x, $y, $block->z));
						$this->level->setBlock(new Vector3($block->x, $y, $block->z), $randomBlock, false, false);
					}
				}
			}
		}
		$this->getUndoStore()->saveUndo($undoBlocks, $this->player);
		return true;
	}
	
	public function getName(): string {
		return "Top Layer";
	}
	
	public function getPermission(): string {
		return "blocksniper.type.toplayer";
	}
}
