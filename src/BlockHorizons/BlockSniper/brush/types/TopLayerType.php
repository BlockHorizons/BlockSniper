<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\brush\types;

use BlockHorizons\BlockSniper\brush\BaseType;
use BlockHorizons\BlockSniper\sessions\SessionManager;
use pocketmine\block\BlockIds;
use pocketmine\block\Flowable;
use pocketmine\item\Item;
use pocketmine\level\ChunkManager;
use pocketmine\math\Facing;
use pocketmine\Player;

/*
 * Replaces the top layer of the terrain, thickness depending on brush height, within the brush radius.
 */

class TopLayerType extends BaseType{

	public const ID = self::TYPE_TOP_LAYER;

	public function __construct(Player $player, ChunkManager $level, \Generator $blocks = null){
		parent::__construct($player, $level, $blocks);
		$this->height = SessionManager::getPlayerSession($player)->getBrush()->height;
	}

	/**
	 * @return \Generator
	 */
	public function fill() : \Generator{
		foreach($this->blocks as $block){
			if($block instanceof Flowable || $block->getId() === Item::AIR){
				continue;
			}

			$higherBlock = $block;
			for($y = $block->y; $y <= $block->y + $this->height; $y++) {
				$higherBlock = $this->side($higherBlock, Facing::UP);
				if($higherBlock instanceof Flowable || $higherBlock->getId() === BlockIds::AIR) {
					yield $block;
					$this->putBlock($block, $this->randomBrushBlock());
					break;
				}
			}
		}
	}

	/**
	 * @return string
	 */
	public function getName() : string{
		return "Top Layer";
	}

	/**
	 * Returns the height/width of the top layer.
	 *
	 * @return int
	 */
	public function getHeight() : int{
		return $this->height;
	}
}
