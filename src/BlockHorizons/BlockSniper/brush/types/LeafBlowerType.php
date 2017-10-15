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

class LeafBlowerType extends BaseType {

	const ID = self::TYPE_LEAF_BLOWER;

	/*
	 * Blows away all plants and flowers within the brush radius.
	 */
	public function __construct(Player $player, ChunkManager $level, array $blocks) {
		parent::__construct($player, $level, $blocks);
	}

	/**
	 * @return Block[]
	 */
	public function fillSynchronously(): array {
		$undoBlocks = [];
		foreach($this->blocks as $block) {
			if($block instanceof Flowable) {
				$undoBlocks[] = $block;
				/** @var Loader $loader */
				$loader = Server::getInstance()->getPluginManager()->getPlugin("BlockSniper");
				if($loader->getSettings()->dropLeafblowerPlants()) {
					$this->getLevel()->dropItem($block, Item::get($block->getId()));
				}
				$this->putBlock($block, 0);
			}
		}
		return $undoBlocks;
	}

	public function fillAsynchronously(): void {
		foreach($this->blocks as $block) {
			if($block instanceof Flowable) {
				$this->putBlock($block, 0);
			}
		}
	}

	public function getName(): string {
		return "Leaf Blower";
	}
}
