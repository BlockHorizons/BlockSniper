<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockSniper\brush\types;

use BlockHorizons\BlockSniper\brush\BaseType;
use BlockHorizons\BlockSniper\Loader;
use pocketmine\block\Block;
use pocketmine\block\Flowable;
use pocketmine\item\Item;
use pocketmine\level\ChunkManager;
use pocketmine\Player;
use pocketmine\Server;

class LeafblowerType extends BaseType {

	/*
	 * Blows away all plants and flowers within the brush radius.
	 */
	public function __construct(Player $player, ChunkManager $level, array $blocks) {
		parent::__construct($player, $level, $blocks);
	}

	/**
	 * @return array
	 */
	public function fillShape(): array {
		$undoBlocks = [];
		foreach($this->blocks as $block) {
			if($block instanceof Flowable) {
				$undoBlocks[] = $block;
				/** @var Loader $loader */
				$loader = Server::getInstance()->getPluginManager()->getPlugin("BlockSniper");
				if($loader->getSettings()->dropLeafblowerPlants()) {
					$this->getLevel()->dropItem($block, Item::get($block->getId()));
				}
				if($this->isAsynchronous()) {
					$this->getChunkManager()->setBlockIdAt($block->x, $block->y, $block->z, Block::AIR);
				} else {
					$this->getLevel()->setBlock($block, Block::get(Block::AIR), false, false);
				}
			}
		}
		return $undoBlocks;
	}

	public function getName(): string {
		return "Leaf Blower";
	}
}
