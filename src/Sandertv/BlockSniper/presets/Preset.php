<?php

namespace Sandertv\BlockSniper\presets;

use pocketmine\Player;
use Sandertv\BlockSniper\brush\Brush;

class Preset {
	
	public $name;
	
	private $shape, $type, $size, $hollow, $decrement;
	private $height, $biome, $obsolete, $blocks;
	
	public function __construct(string $name, string $shape = null, string $type = null, bool $decrement = null, int $size = null, bool $hollow = null, array $blocks = null, array $obsolete = null, int $height = null, string $biome = null) {
		$this->name = $name;
		
		$this->shape = $shape;
		$this->type = $type;
		$this->decrement = $decrement;
		$this->size = $size;
		$this->hollow = $hollow;
		
		$this->height = $height;
		$this->biome = $biome;
		$this->obsolete = $obsolete;
		$this->blocks = $blocks;
	}
	
	/**
	 * Applies the preset on a player.
	 *
	 * @param Player $player
	 */
	public function apply(Player $player) {
		foreach($this->getParsedData() as $property => $value) {
			Brush::$brush[$player->getId()][$property] = $value;
		}
	}
	
	/**
	 * @return array
	 */
	public function getParsedData(): array {
		$data = [];
		$data["shape"] = $this->shape;
		$data["type"] = $this->type;
		$data["decrement"] = $this->decrement;
		$data["size"] = $this->size;
		$data["hollow"] = $this->hollow;
		$data["blocks"] = $this->blocks;
		$data["height"] = $this->height;
		$data["biome"] = $this->biome;
		$data["obsolete"] = $this->obsolete;
		
		return $data;
	}
}