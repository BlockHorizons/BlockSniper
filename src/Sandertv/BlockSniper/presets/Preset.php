<?php

namespace Sandertv\BlockSniper\presets;

use pocketmine\Player;
use Sandertv\BlockSniper\brush\Brush;

class Preset {
	
	public $name;
	
	private $shape, $type, $size, $hollow, $decrement;
	private $height, $biome, $obsolete, $blocks;
	
	public function __construct(string $name, string $shape = null, string $type = null, bool $decrement = null, int $size = null, bool $hollow = null, string $blocks = null, string $obsolete = null, int $height = null, string $biome = null) {
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
			switch($property) {
				case "shape":
					Brush::setShape($player, $value);
					break;
				case "type":
					Brush::setType($player, $value);
					break;
				case "decrement":
					Brush::setDecrementing($player, $value);
					break;
				case "size":
					Brush::setSize($player, $value);
					break;
				case "hollow":
					Brush::setHollow($player, $value);
					break;
				case "height":
					Brush::setHeight($player, $value);
					break;
				case "biome":
					Brush::setBiome($player, $value);
					break;
				case "obsolete":
					$value = explode(",", $value);
					Brush::setObsolete($player, $value);
					break;
				case "blocks":
					$value = explode(",", $value);
					Brush::setBlocks($player, $value);
					break;
			}
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