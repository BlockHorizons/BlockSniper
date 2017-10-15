<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockSniper\brush\types;

use BlockHorizons\BlockSniper\brush\BaseType;
use BlockHorizons\BlockSniper\sessions\SessionManager;
use pocketmine\block\Block;
use pocketmine\block\Flowable;
use pocketmine\item\Item;
use pocketmine\level\ChunkManager;
use pocketmine\math\Vector3;
use pocketmine\Player;

class TopLayerType extends BaseType {

	const ID = self::TYPE_TOP_LAYER;

	/*
	 * Replaces the top layer of the terrain, thickness depending on brush height, within the brush radius.
	 */
	public function __construct(Player $player, ChunkManager $level, array $blocks) {
		parent::__construct($player, $level, $blocks);
		$this->height = SessionManager::getPlayerSession($player)->getBrush()->getHeight();
	}

	/**
	 * @return Block[]
	 */
	public function fillSynchronously(): array {
		$undoBlocks = [];
		foreach($this->blocks as $block) {
			if($block->getId() !== Item::AIR && !$block instanceof Flowable) {
				$up = $block->getSide(Block::SIDE_UP);
				if($up->getId() === Item::AIR || $up instanceof Flowable) {
					$randomBlock = $this->brushBlocks[array_rand($this->brushBlocks)];
					for($y = $block->y; $y >= $block->y - $this->height; $y--) {
						$undoBlocks[] = $this->getLevel()->getBlock(new Vector3($block->x, $y, $block->z));
						$this->putBlock($block, $randomBlock->getId(), $randomBlock->getDamage());
					}
				}
			}
		}
		return $undoBlocks;
	}

	public function fillAsynchronously(): void {
		foreach($this->blocks as $block) {
			if($block->getId() !== Item::AIR && !$block instanceof Flowable) {
				$up = $this->getChunkManager()->getSide($block->x, $block->y, $block->z, Block::SIDE_UP);
				if($up->getId() === Item::AIR || $up instanceof Flowable) {
					$randomBlock = $this->brushBlocks[array_rand($this->brushBlocks)];
					for($y = $block->y; $y >= $block->y - $this->height; $y--) {
						$this->putBlock($block, $randomBlock->getId(), $randomBlock->getDamage());
					}
				}
			}
		}
	}

	public function getName(): string {
		return "Top Layer";
	}

	/**
	 * Returns the height/width of the top layer.
	 *
	 * @return int
	 */
	public function getHeight(): int {
		return $this->height;
	}
}
