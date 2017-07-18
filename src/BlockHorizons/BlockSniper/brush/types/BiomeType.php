<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockSniper\brush\types;

use BlockHorizons\BlockSniper\brush\BaseType;
use BlockHorizons\BlockSniper\brush\BrushManager;
use pocketmine\level\ChunkManager;
use pocketmine\Player;

class BiomeType extends BaseType {

	/*
	 * Changes the biome within the brush radius.
	 */
	public function __construct(Player $player, ChunkManager $level, array $blocks) {
		parent::__construct($player, $level, $blocks);
		$this->biome = BrushManager::get($player)->getBiomeId();
	}

	/**
	 * @return array
	 */
	public function fillShape(): array {
		foreach($this->blocks as $block) {
			if($this->isAsynchronous()) {
				$this->getChunkManager()->setBiomeIdAt($block->x & 0x0f, $block->z & 0x0f, $this->biome);
			} else {
				$this->getLevel()->setBiomeId($block->x, $block->z, $this->biome);
			}
		}
		return [];
	}

	public function getName(): string {
		return "Biome";
	}

	/**
	 * Returns the biome ID of this type.
	 *
	 * @return int
	 */
	public function getBiome(): int {
		return $this->biome;
	}
}
