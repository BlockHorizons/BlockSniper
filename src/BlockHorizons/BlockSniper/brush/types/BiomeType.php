<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockSniper\brush\types;

use BlockHorizons\BlockSniper\brush\BaseType;
use BlockHorizons\BlockSniper\sessions\SessionManager;
use pocketmine\block\Block;
use pocketmine\level\ChunkManager;
use pocketmine\Player;

class BiomeType extends BaseType {

	const ID = self::TYPE_BIOME;

	/** @var int */
	protected $biome = 0;

	/*
	 * Changes the biome within the brush radius.
	 */
	public function __construct(Player $player, ChunkManager $level, array $blocks) {
		parent::__construct($player, $level, $blocks);
		$this->biome = SessionManager::getPlayerSession($player)->getBrush()->getBiomeId();
	}

	/**
	 * @return Block[]
	 */
	protected function fillSynchronously(): array {
		foreach($this->blocks as $block) {
			$this->putBiome($block, $this->biome);
		}
		return [];
	}

	protected function fillAsynchronously(): void {
		foreach($this->blocks as $block) {
			$this->putBiome($block, $this->biome);
		}
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
