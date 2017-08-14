<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockSniper\presets;

use BlockHorizons\BlockSniper\brush\PropertyProcessor;
use BlockHorizons\BlockSniper\Loader;
use pocketmine\Player;

class Preset {

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

	/** @var string */
	public $name = "";
	/** @var array */
	private $data = [];

	public function __construct(string $name, array $data) {
		$this->name = $name;
		$this->data = $data;
	}

	/**
	 * Applies the preset on a player.
	 *
	 * @param Player $player
	 * @param Loader $loader
	 */
	public function apply(Player $player, Loader $loader) {
		$processor = new PropertyProcessor($player, $loader);
		foreach($this->data as $index => $value) {
			$processor->process($index - 1, $value);
		}
	}
}