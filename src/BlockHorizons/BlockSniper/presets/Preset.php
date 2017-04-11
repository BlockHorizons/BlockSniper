<?php

namespace BlockHorizons\BlockSniper\presets;

use BlockHorizons\BlockSniper\brush\BrushManager;
use pocketmine\Player;

class Preset {
	
	public $name;
	
	private $shape, $type, $size, $hollow, $decrement;
	private $height, $biome, $obsolete, $blocks;
	
	public function __construct(string $name, string $shape = null, string $type = null, bool $decrement = null, bool $perfect = null, int $size = null, bool $hollow = null, array $blocks = null, array $obsolete = null, int $height = null, string $biome = null) {
		$this->name = $name;
		
		$this->shape = $shape;
		$this->type = $type;
		$this->decrement = $decrement;
		$this->size = $size;
		$this->hollow = $hollow;
		$this->perfect = $perfect;
		
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
		$brush = BrushManager::get($player);
		foreach($this->getParsedData() as $property => $value) {
			switch($property) {
				case "shape":
					$brush->setShape($value);
					break;
				case "type":
					$brush->setType($value);
					break;
				case "decrement":
					$brush->setDecrementing($value);
					break;
				case "size":
					$brush->setSize($value);
					break;
				case "hollow":
					$brush->setHollow($value);
					break;
				case "height":
					$brush->setHeight($value);
					break;
				case "biome":
					$brush->setBiome($value);
					break;
				case "obsolete":
					$brush->setObsolete($value);
					break;
				case "blocks":
					$brush->setBlocks($value);
					break;
				case "perfect":
					$brush->setPerfect($value);
					break;
			}
		}
	}
	
	/**
	 * @return array
	 */
	public function getParsedData(): array {
		$data = [];
		$data["name"] = $this->name;
		$data["shape"] = $this->shape;
		$data["type"] = $this->type;
		$data["decrement"] = $this->decrement;
		$data["perfect"] = $this->perfect;
		$data["size"] = $this->size;
		$data["hollow"] = $this->hollow;
		$data["blocks"] = $this->blocks;
		$data["height"] = $this->height;
		$data["biome"] = $this->biome;
		$data["obsolete"] = $this->obsolete;
		
		return $data;
	}
}