<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockSniper\brush\types;

use BlockHorizons\BlockSniper\brush\BaseType;
use BlockHorizons\BlockSniper\sessions\SessionManager;
use pocketmine\level\ChunkManager;
use pocketmine\Player;

class BiomeType extends BaseType {

	/** @var int */
	protected $id = self::TYPE_BIOME;
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

	public function fillAsynchronously(): void {
		return;
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

	/**
	 * @return bool
	 */
	public function canExecuteAsynchronously(): bool {
		return false;
	}
}
