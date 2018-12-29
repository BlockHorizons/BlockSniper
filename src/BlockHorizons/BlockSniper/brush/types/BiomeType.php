<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\brush\types;

use BlockHorizons\BlockSniper\brush\BaseType;
use BlockHorizons\BlockSniper\sessions\SessionManager;
use pocketmine\level\ChunkManager;
use pocketmine\Player;

/*
 * Changes the biome within the brush radius.
 */

class BiomeType extends BaseType{

	public const ID = self::TYPE_BIOME;

	public function __construct(Player $player, ChunkManager $level, \Generator $blocks = null){
		parent::__construct($player, $level, $blocks);
		$this->biome = SessionManager::getPlayerSession($player)->getBrush()->biomeId;
	}

	/**
	 * @return \Generator
	 */
	protected function fill() : \Generator{
		foreach($this->blocks as $block){
			$this->putBiome($block, $this->biome);
		}
		if(false){
			// Make PHP recognize this is a generator.
			yield;
		}
	}

	/**
	 * @return string
	 */
	public function getName() : string{
		return "Biome";
	}

	/**
	 * @return bool
	 */
	public function usesBlocks() : bool{
		return false;
	}

	/**
	 * Returns the biome of this type.
	 *
	 * @return int
	 */
	public function getBiome() : int{
		return $this->biome;
	}
}
