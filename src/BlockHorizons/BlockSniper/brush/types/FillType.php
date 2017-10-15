<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockSniper\brush\types;

use BlockHorizons\BlockSniper\brush\BaseType;
use pocketmine\block\Block;
use pocketmine\level\ChunkManager;
use pocketmine\Player;

class FillType extends BaseType {

	const ID = self::TYPE_FILL;

	/*
	 * Places blocks on every location within the brush radius.
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
			$randomBlock = $this->brushBlocks[array_rand($this->brushBlocks)];
			$undoBlocks[] = $block;
			$this->putBlock($block, $randomBlock->getId(), $randomBlock->getDamage());
		}
		return $undoBlocks;
	}

	public function fillAsynchronously(): void {
		foreach($this->blocks as $block) {
			$randomBlock = $this->brushBlocks[array_rand($this->brushBlocks)];
			$this->putBlock($block, $randomBlock->getId(), $randomBlock->getDamage());
		}
	}

	public function getName(): string {
		return "Fill";
	}
}
