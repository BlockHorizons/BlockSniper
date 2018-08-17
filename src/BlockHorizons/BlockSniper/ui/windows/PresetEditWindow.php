<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockSniper\ui\windows;

use BlockHorizons\BlockSniper\data\Translation;
use BlockHorizons\BlockSniper\presets\Preset;
use BlockHorizons\BlockSniper\presets\PresetPropertyProcessor;
use BlockHorizons\BlockSniper\ui\WindowHandler;
use pocketmine\network\mcpe\protocol\ModalFormResponsePacket;

class PresetEditWindow extends CustomWindow {

	/** @var null|Preset */
	private $preset = null;

	public function process(): void {
		if($this->preset === null) {
			return;
		}
		$shapes = $this->processShapes();
		$types = $this->processTypes();
		$d = $this->preset->getData();
		$shapeKey = array_search($d[2], $shapes, true);
		$typeKey = array_search($d[3], $types, true);

		$this->data = [
			"type" => "custom_form",
			"title" => Translation::get(Translation::UI_PRESET_EDIT_TITLE),
			"content" => [
				[
					"type" => "input",
					"text" => Translation::get(Translation::UI_PRESET_EDIT_NAME),
					"default" => $d[0],
					"placeholder" => Translation::get(Translation::UI_PRESET_EDIT_NAME)
				],
				[
					"type" => "slider",
					"text" => Translation::get(Translation::UI_PRESET_EDIT_SIZE),
					"min" => 0,
					"max" => $this->loader->config->MaximumSize,
					"step" => 1,
					"default" => $d[1]
				],
				[
					"type" => "dropdown",
					"text" => Translation::get(Translation::UI_PRESET_EDIT_SHAPE),
					"default" => $shapeKey === false ? 0 : $shapeKey,
					"options" => $shapes
				],
				[
					"type" => "dropdown",
					"text" => Translation::get(Translation::UI_PRESET_EDIT_TYPE),
					"default" => $typeKey === false ? 0 : $typeKey,
					"options" => $types
				],
				[
					"type" => "toggle",
					"text" => Translation::get(Translation::UI_PRESET_EDIT_HOLLOW),
					"default" => $d[4]
				],
				[
					"type" => "toggle",
					"text" => Translation::get(Translation::UI_PRESET_EDIT_DECREMENT),
					"default" => $d[5]
				],
				[
					"type" => "slider",
					"text" => Translation::get(Translation::UI_PRESET_EDIT_HEIGHT),
					"min" => 0,
					"max" => $this->loader->config->MaximumSize,
					"step" => 1,
					"default" => $d[6]
				],
				[
					"type" => "toggle",
					"text" => Translation::get(Translation::UI_PRESET_EDIT_PERFECT),
					"default" => $d[7]
				],
				[
					"type" => "input",
					"text" => Translation::get(Translation::UI_PRESET_EDIT_BLOCKS),
					"placeholder" => "stone,stone_brick:1,2",
					"default" => $d[8]
				],
				[
					"type" => "input",
					"text" => Translation::get(Translation::UI_PRESET_EDIT_OBSOLETE),
					"placeholder" => "stone,stone_brick:1,2",
					"default" => $d[9]
				],
				[
					"type" => "input",
					"text" => Translation::get(Translation::UI_PRESET_EDIT_BIOME),
					"placeholder" => "plains",
					"default" => $d[10]
				],
				[
					"type" => "input",
					"text" => Translation::get(Translation::UI_PRESET_EDIT_TREE),
					"placeholder" => "oak",
					"default" => $d[11]
				]
			]
		];
	}

	/**
	 * @return Preset
	 */
	public function getPreset(): Preset {
		return $this->preset;
	}

	/**
	 * @param Preset $preset
	 */
	public function setPreset(Preset $preset) {
		$this->preset = $preset;
	}

	public function handle(ModalFormResponsePacket $packet): bool {
		$data = json_decode($packet->formData, true);
		$processor = new PresetPropertyProcessor($this->player, $this->loader);
		foreach($data as $key => $value) {
			$processor->process($key, $value);
		}
		$this->navigate(WindowHandler::WINDOW_PRESET_LIST_MENU, $this->player, new WindowHandler());
		return true;
	}
}