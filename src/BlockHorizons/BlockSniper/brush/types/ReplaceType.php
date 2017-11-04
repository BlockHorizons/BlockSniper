<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockSniper\brush\types;

use BlockHorizons\BlockSniper\brush\BaseType;
use BlockHorizons\BlockSniper\sessions\SessionManager;
use pocketmine\block\Block;
use pocketmine\level\ChunkManager;
use pocketmine\Player;

/*
 * Replaces the obsolete blocks within the brush radius.
 */

class ReplaceType extends BaseType {

	const ID = self::TYPE_REPLACE;

	public function __construct(Player $player, ChunkManager $level, array $blocks) {
		parent::__construct($player, $level, $blocks);
		$this->obsolete = SessionManager::getPlayerSession($player)->getBrush()->getObsolete();
	}

	/**
	 * @return Block[]
	 */
	public function fillSynchronously(): array {
		$undoBlocks = [];
		foreach($this->blocks as $block) {
			$randomBlock = $this->brushBlocks[array_rand($this->brushBlocks)];
			foreach($this->obsolete as $obsolete) {
				if($block->getId() === $obsolete->getId()) {
					$undoBlocks[] = $block;
					$this->putBlock($block, $randomBlock->getId(), $randomBlock->getDamage());
				}
			}
		}
		return $undoBlocks;
	}

	public function fillAsynchronously(): void {
		foreach($this->blocks as $block) {
			$randomBlock = $this->brushBlocks[array_rand($this->brushBlocks)];
			foreach($this->obsolete as $obsolete) {
				if($block->getId() === $obsolete->getId()) {
					$this->putBlock($block, $randomBlock->getId(), $randomBlock->getDamage());
				}
			}
		}
	}

	public function getName(): string {
		return "Replace";
	}

	/**
	 * Returns the obsolete blocks of this type.
	 *
	 * @return array
	 */
	public function getObsolete(): array {
		return $this->obsolete;
	}
}

