<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockSniper\ui\windows;

use BlockHorizons\BlockSniper\presets\Preset;

class PresetEditWindow extends Window {

	/** @var null|Preset */
	private $preset = null;

	public function process() {
		if($this->preset === null) {
			return;
		}
		$shapes = $this->processShapes();
		$types = $this->processTypes();
		$d = $this->preset->getData();
		$shapeKey = array_search($d[2], $shapes);
		$typeKey = array_search($d[3], $types);

		$this->data = [
			"type" => "custom_form",
			"title" => "Preset Edit Menu",
			"content" => [
				[
					"type" => "input",
					"text" => "Preset Name",
					"default" => $d[0],
					"placeholder" => "Preset Name"
				],
				[
					"type" => "slider",
					"text" => "Brush Size",
					"min" => 0,
					"max" => $this->getLoader()->getSettings()->getMaxRadius(),
					"step" => 1,
					"default" => $d[1]
				],
				[
					"type" => "dropdown",
					"text" => "Brush Shape",
					"default" => ($shapeKey === false ? 0 : $shapeKey),
					"options" => $shapes
				],
				[
					"type" => "dropdown",
					"text" => "Brush Type",
					"default" => ($typeKey === false ? 0 : $typeKey),
					"options" => $types
				],
				[
					"type" => "toggle",
					"text" => "Hollow Brush",
					"default" => $d[4]
				],
				[
					"type" => "toggle",
					"text" => "Brush Decrement",
					"default" => $d[5]
				],
				[
					"type" => "slider",
					"text" => "Brush Height",
					"min" => 0,
					"max" => $this->getLoader()->getSettings()->getMaxRadius(),
					"step" => 1,
					"default" => $d[6]
				],
				[
					"type" => "toggle",
					"text" => "Brush Shape Perfection",
					"default" => $d[7]
				],
				[
					"type" => "input",
					"text" => "Brush Blocks",
					"placeholder" => "stone,stone_brick:1,2",
					"default" => $d[8]
				],
				[
					"type" => "input",
					"text" => "Obsolete Blocks",
					"placeholder" => "stone,stone_brick:1,2",
					"default" => $d[9]
				],
				[
					"type" => "input",
					"text" => "Brush Biome",
					"placeholder" => "plains",
					"default" => $d[10]
				],
				[
					"type" => "input",
					"text" => "Brush Tree",
					"placeholder" => "oak",
					"default" => $d[11]
				]
			]
		];
	}

	/**
	 * @param Preset $preset
	 */
	public function setPreset(Preset $preset) {
		$this->preset = $preset;
	}

	/**
	 * @return Preset
	 */
	public function getPreset(): Preset {
		return $this->preset;
	}
}