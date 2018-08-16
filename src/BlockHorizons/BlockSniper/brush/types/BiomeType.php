<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockSniper\brush\types;

use BlockHorizons\BlockSniper\brush\BaseType;
use BlockHorizons\BlockSniper\sessions\SessionManager;
use pocketmine\block\Block;
use pocketmine\level\biome\Biome;
use pocketmine\level\ChunkManager;
use pocketmine\Player;

/*
 * Changes the biome within the brush radius.
 */

class BiomeType extends BaseType {

	const ID = self::TYPE_BIOME;

	public function __construct(Player $player, ChunkManager $level, array $blocks) {
		parent::__construct($player, $level, $blocks);
		$this->biome = SessionManager::getPlayerSession($player)->getBrush()->biome;
	}

	public function getName(): string {
		return "Biome";
	}

	/**
	 * Returns the biome of this type.
	 *
	 * @return Biome
	 */
	public function getBiome(): Biome {
		return $this->biome;
	}

	/**
	 * @return Block[]
	 */
	protected function fillSynchronously(): array {
		foreach($this->blocks as $block) {
			$this->putBiome($block, $this->biome->getId());
		}
		return [];
	}

	protected function fillAsynchronously(): void {
		foreach($this->blocks as $block) {
			$this->putBiome($block, $this->biome->getId());
		}
	}
}
