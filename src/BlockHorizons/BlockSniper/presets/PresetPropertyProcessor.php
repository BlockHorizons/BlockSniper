<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockSniper\presets;

use BlockHorizons\BlockSniper\Loader;
use pocketmine\Player;

class PresetPropertyProcessor {

	const VALUE_NAME = 0;
	const VALUE_SIZE = 1;
	const VALUE_SHAPE = 2;
	const VALUE_TYPE = 3;
	const VALUE_HOLLOW = 4;
	const VALUE_DECREMENT = 5;
	const VALUE_HEIGHT = 6;
	const VALUE_PERFECT = 7;
	const VALUE_BLOCKS = 8;
	const VALUE_OBSOLETE = 9;
	const VALUE_BIOME = 10;
	const VALUE_TREE = 11;

	/** @var Player */
	private $player = null;
	/** @var Loader */
	private $loader = null;
	/** @var array */
	private $properties = [];

	public function __construct(Player $player, Loader $loader) {
		$this->player = $player;
		$this->loader = $loader;
	}

	/**
	 * @return Loader
	 */
	public function getLoader(): Loader {
		return $this->loader;
	}

	/**
	 * @param int $valueType
	 * @param     $value
	 */
	public function process(int $valueType, $value): void {
		$this->properties[$valueType] = $value;

		if(count($this->properties) === 12) {
			$this->getLoader()->getPresetManager()->addPreset(new Preset($this->properties[self::VALUE_NAME], $this->properties));
		}
	}
}