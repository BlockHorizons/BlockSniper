<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockSniper\user_interface;

use BlockHorizons\BlockSniper\brush\BrushManager;
use BlockHorizons\BlockSniper\Loader;
use pocketmine\level\generator\biome\Biome;
use pocketmine\Player;

class WindowHandler {

	const WINDOW_MAIN_MENU = 0;
	const WINDOW_BRUSH_MENU = 1;
	const WINDOW_PRESET_MENU = 2;
	const WINDOW_CONFIGURATION_MENU = 3;
	const WINDOW_PRESET_CREATION_MENU = 4;
	const WINDOW_PRESET_SELECTION_MENU = 5;
	const WINDOW_PRESET_DELETION_MENU = 6;
	const WINDOW_PRESET_LIST_MENU = 7;

	/** @var array */
	private $data = [];

	public function __construct() {
		$this->data = json_decode(file_get_contents(__DIR__ . "\BrushWindows.json"), true);
	}

	/**
	 * @param int $windowId
	 *
	 * @return string
	 */
	public function getWindowJson(int $windowId): string {
		if(isset($this->data[$windowId])) {
			return json_encode($this->data[$windowId]);
		}
		return "";
	}

	/**
	 * @param int $windowId
	 *
	 * @return int
	 */
	public function getWindowIdFor(int $windowId): int {
		return 3200 + $windowId;
	}

	/**
	 * @param Player $player
	 * @param Loader $loader
	 *
	 * @return string
	 */
	public function getBrushWindowJson(Player $player, Loader $loader): string {
		$v = BrushManager::get($player);
		$blocks = [];
		foreach($v->getBlocks() as $block) {
			$blocks[] = $block->getId() . ":" . $block->getDamage();
		}
		$blocks = implode(",", $blocks);
		$obsoletes = [];
		foreach($v->getObsolete() as $obsolete) {
			$obsoletes[] = $obsolete->getId() . ":" . $obsolete->getDamage();
		}
		$shapes = [
			"Sphere",
			"Cube",
			"Cuboid",
			"Standing Cylinder"
		];
		foreach($shapes as $key => $shape) {
			if(!$player->hasPermission("blocksniper.shape." . strtolower(str_replace(" ", "", $shape)))) {
				unset($shapes[$key]);
			}
		}
		$types = [
			"Biome",
			"CleanEntities",
			"Clean",
			"Drain",
			"Expand",
			"Fill",
			"FlattenAll",
			"Flatten",
			"Layer",
			"LeafBlower",
			"Melt",
			"Overlay",
			"ReplaceAll",
			"Replace",
			"Snowcone",
			"TopLayer",
			"Tree"
		];
		foreach($types as $key => $type) {
			if(!$player->hasPermission("blocksniper.type." . strtolower(str_replace(" ", "", $type)))) {
				unset($types[$key]);
			}
		}
		$obsoletes = implode(",", $obsoletes);
		$this->data[self::WINDOW_BRUSH_MENU]["content"][0]["max"] = $loader->getSettings()->getMaxRadius();
		$this->data[self::WINDOW_BRUSH_MENU]["content"][0]["default"] = $v->getSize();
		$this->data[self::WINDOW_BRUSH_MENU]["content"][1]["options"] = $shapes;
		$this->data[self::WINDOW_BRUSH_MENU]["content"][1]["default"] = $v->getShape()->getId();
		$this->data[self::WINDOW_BRUSH_MENU]["content"][2]["default"] = $v->getType()->getId();
		$this->data[self::WINDOW_BRUSH_MENU]["content"][2]["options"] = $types;
		$this->data[self::WINDOW_BRUSH_MENU]["content"][3]["default"] =	$v->getHollow();
		$this->data[self::WINDOW_BRUSH_MENU]["content"][4]["default"] = $v->isDecrementing();
		$this->data[self::WINDOW_BRUSH_MENU]["content"][5]["max"] = $loader->getSettings()->getMaxRadius();
		$this->data[self::WINDOW_BRUSH_MENU]["content"][5]["default"] = $v->getHeight();
		$this->data[self::WINDOW_BRUSH_MENU]["content"][6]["default"] = $v->getPerfect();
		$this->data[self::WINDOW_BRUSH_MENU]["content"][7]["default"] = $blocks;
		$this->data[self::WINDOW_BRUSH_MENU]["content"][8]["default"] = $obsoletes;
		$this->data[self::WINDOW_BRUSH_MENU]["content"][9]["default"] = strtolower(Biome::getBiome($v->getBiomeId())->getName());
		$this->data[self::WINDOW_BRUSH_MENU]["content"][10]["default"] = (string) $v->getTreeType();

		return $this->getWindowJson(self::WINDOW_BRUSH_MENU);
	}

	/**
	 * @param Loader $loader
	 *
	 * @return string
	 */
	public function getConfigurationWindowJson(Loader $loader): string {
		$s = $loader->getSettings();
		$key = array_search($s->getLanguage(), $loader::getAvailableLanguages());
		$this->data[self::WINDOW_CONFIGURATION_MENU]["content"][0]["default"] = $s->updatesAutomatically();
		$this->data[self::WINDOW_CONFIGURATION_MENU]["content"][1]["default"] = ($key === false ? 0 : $key);
		$this->data[self::WINDOW_CONFIGURATION_MENU]["content"][1]["options"] = $loader::getAvailableLanguages();
		$this->data[self::WINDOW_CONFIGURATION_MENU]["content"][2]["default"] = $s->getBrushItem();
		$this->data[self::WINDOW_CONFIGURATION_MENU]["content"][3]["default"] = $s->getMaxRadius();
		$this->data[self::WINDOW_CONFIGURATION_MENU]["content"][4]["default"] = $s->getMinimumAsynchronousSize();
		$this->data[self::WINDOW_CONFIGURATION_MENU]["content"][5]["default"] = $s->getMaxUndoStores();
		$this->data[self::WINDOW_CONFIGURATION_MENU]["content"][6]["default"] = $s->resetDecrementBrush();
		$this->data[self::WINDOW_CONFIGURATION_MENU]["content"][7]["default"] = $s->saveBrushProperties();
		$this->data[self::WINDOW_CONFIGURATION_MENU]["content"][8]["default"] = $s->dropLeafblowerPlants();

		return $this->getWindowJson(self::WINDOW_CONFIGURATION_MENU);
	}

	/**
	 * @param Player $player
	 * @param Loader $loader
	 *
	 * @return string
	 */
	public function getPresetCreationWindowJson(Player $player, Loader $loader): string {
		$shapes = [
			"Sphere",
			"Cube",
			"Cuboid",
			"Standing Cylinder"
		];
		foreach($shapes as $key => $shape) {
			if(!$player->hasPermission("blocksniper.shape." . strtolower(str_replace(" ", "", $shape)))) {
				unset($shapes[$key]);
			}
		}
		$types = [
			"Biome",
			"CleanEntities",
			"Clean",
			"Drain",
			"Expand",
			"Fill",
			"FlattenAll",
			"Flatten",
			"Layer",
			"LeafBlower",
			"Melt",
			"Overlay",
			"ReplaceAll",
			"Replace",
			"Snowcone",
			"TopLayer",
			"Tree"
		];
		foreach($types as $key => $type) {
			if(!$player->hasPermission("blocksniper.type." . strtolower(str_replace(" ", "", $type)))) {
				unset($types[$key]);
			}
		}
		$this->data[self::WINDOW_PRESET_CREATION_MENU]["content"][1]["max"] = $loader->getSettings()->getMaxRadius();
		$this->data[self::WINDOW_PRESET_CREATION_MENU]["content"][2]["options"] = $shapes;
		$this->data[self::WINDOW_PRESET_CREATION_MENU]["content"][3]["options"] = $types;
		$this->data[self::WINDOW_PRESET_CREATION_MENU]["content"][6]["max"] = $loader->getSettings()->getMaxRadius();

		return $this->getWindowJson(self::WINDOW_PRESET_CREATION_MENU);
	}

	/**
	 * @param Loader $loader
	 *
	 * @return string
	 */
	public function getPresetSelectionMenuJson(Loader $loader): string {
		$presets = $loader->getPresetManager()->getAllPresets();
		$json = [
			"type" => "form",
			"title" => "Preset Selection Menu",
			"content" => "Select a preset to apply.",
			"buttons" => []
		];
		foreach($presets as $key => $name) {
			$json["buttons"][$key] = [
				"text" => $name,
				"image" => [
					"type" => "url",
					"data" => "http://www.clker.com/cliparts/k/T/w/u/G/S/transparent-yellow-checkmark-md.png"
				]
			];
		}
		return json_encode($json);
	}

	/**
	 * @param Loader $loader
	 *
	 * @return string
	 */
	public function getPresetDeletionMenuJson(Loader $loader): string {
		$presets = $loader->getPresetManager()->getAllPresets();
		$json = [
			"type" => "form",
			"title" => "Preset Deletion Menu",
			"content" => "Select a preset to delete.",
			"buttons" => []
		];
		foreach($presets as $key => $name) {
			$json["buttons"][$key] = [
				"text" => $name,
				"image" => [
					"type" => "url",
					"data" => "http://www.pngmart.com/files/3/Red-Cross-Transparent-PNG.png"
				]
			];
		}
		return json_encode($json);
	}
}