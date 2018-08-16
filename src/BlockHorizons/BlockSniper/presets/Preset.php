<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockSniper\presets;

use BlockHorizons\BlockSniper\brush\PropertyProcessor;
use BlockHorizons\BlockSniper\brush\shapes\SphereShape;
use BlockHorizons\BlockSniper\brush\types\FillType;
use BlockHorizons\BlockSniper\Loader;
use BlockHorizons\BlockSniper\sessions\SessionManager;
use pocketmine\Player;

class Preset {
	public $size = 0;
	public $shape = SphereShape::ID;
	public $type = FillType::ID;
	public $hollow = false;
	public $decrement = false;
	public $height = 0;
	public $blocks = "stone";
	public $obsolete = "stone";
	public $biome = "plains";
	public $tree = "oak";

	/** @var string */
	public $name = "";

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
	public function apply(Player $player, Loader $loader): void {
		$processor = new PropertyProcessor(SessionManager::getPlayerSession($player), $loader);
		foreach($this->data as $index => $value) {
			$processor->process($index - 1, $value);
		}
	}

	/**
	 * @return array
	 */
	public function getData(): array {
		return $this->data;
	}
}