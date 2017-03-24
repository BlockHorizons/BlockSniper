<?php

namespace Sandertv\BlockSniper\presets;

use pocketmine\Player;
use Sandertv\BlockSniper\brush\BrushManager;

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
		foreach($this->getParsedData() as $property => $value) {
			switch($property) {
				case "shape":
					BrushManager::get($player)->setShape($value);
					break;
				case "type":
					BrushManager::get($player)->setType($value);
					break;
				case "decrement":
					BrushManager::get($player)->setDecrementing($value);
					break;
				case "size":
					BrushManager::get($player)->setSize($value);
					break;
				case "hollow":
					BrushManager::get($player)->setHollow($value);
					break;
				case "height":
					BrushManager::get($player)->setHeight($value);
					break;
				case "biome":
					BrushManager::get($player)->setBiome($value);
					break;
				case "obsolete":
					BrushManager::get($player)->setObsolete($value);
					break;
				case "blocks":
					BrushManager::get($player)->setBlocks($value);
					break;
				case "perfect":
					BrushManager::get($player)->setPerfect($value);
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