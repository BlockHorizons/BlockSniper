<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockSniper\brush\types;

use BlockHorizons\BlockSniper\brush\BaseType;
use BlockHorizons\BlockSniper\sessions\SessionManager;
use pocketmine\block\Block;
use pocketmine\level\ChunkManager;
use pocketmine\Player;

class ReplaceType extends BaseType {

	/** @var int */
	protected $id = self::TYPE_REPLACE;
	/** @var Block[] */
	protected $obsolete = [];

	/*
	 * Replaces the obsolete blocks within the brush radius.
	 */
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
					if($block->getId() !== $randomBlock->getId()) {
						$undoBlocks[] = $block;
					}
					$this->getLevel()->setBlock($block, $randomBlock, false, false);
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
					$this->getChunkManager()->setBlockIdAt($block->x, $block->y, $block->z, $randomBlock->getId());
					$this->getChunkManager()->setBlockDataAt($block->x, $block->y, $block->z, $randomBlock->getDamage());
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

