<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\brush\type;

use BlockHorizons\BlockSniper\brush\BrushProperties;
use BlockHorizons\BlockSniper\brush\Target;
use BlockHorizons\BlockSniper\brush\Type;
use Generator;

/*
 * Changes the biome within the brush radius.
 */

class BiomeType extends Type{

	public const ID = self::TYPE_BIOME;

	/** @var int */
	private $biome;

	public function __construct(BrushProperties $properties, Target $target, Generator $blocks = null){
		parent::__construct($properties, $target, $blocks);
		$this->biome = $properties->biomeId;
	}

	/**
	 * @return Generator
	 */
	protected function fill() : Generator{
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
	public function usesBrushBlocks() : bool{
		return false;
	}
}
