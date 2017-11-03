<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockSniper\ui\windows;

use BlockHorizons\BlockSniper\data\Translation;
use BlockHorizons\BlockSniper\presets\PresetPropertyProcessor;
use BlockHorizons\BlockSniper\ui\WindowHandler;
use pocketmine\network\mcpe\protocol\ModalFormResponsePacket;

class PresetCreationWindow extends Window {

	public function process(): void {
		$this->data = [
			"type" => "custom_form",
			"title" => Translation::get(Translation::UI_PRESET_CREATION_TITLE),
			"content" => [
				[
					"type" => "input",
					"text" => Translation::get(Translation::UI_PRESET_CREATION_NAME),
					"default" => "New Preset",
					"placeholder" => Translation::get(Translation::UI_PRESET_CREATION_NAME)
				],
				[
					"type" => "slider",
					"text" => Translation::get(Translation::UI_PRESET_CREATION_SIZE),
					"min" => 0,
					"max" => $this->getLoader()->getSettings()->getMaxRadius(),
					"step" => 1,
					"default" => 0
				],
				[
					"type" => "dropdown",
					"text" => Translation::get(Translation::UI_PRESET_CREATION_SHAPE),
					"default" => 0,
					"options" => $this->processShapes()
				],
				[
					"type" => "dropdown",
					"text" => Translation::get(Translation::UI_PRESET_CREATION_TYPE),
					"default" => 5,
					"options" => $this->processTypes()
				],
				[
					"type" => "toggle",
					"text" => Translation::get(Translation::UI_PRESET_CREATION_HOLLOW),
					"default" => false
				],
				[
					"type" => "toggle",
					"text" => Translation::get(Translation::UI_PRESET_CREATION_DECREMENT),
					"default" => false
				],
				[
					"type" => "slider",
					"text" => Translation::get(Translation::UI_PRESET_CREATION_HEIGHT),
					"min" => 0,
					"max" => $this->getLoader()->getSettings()->getMaxRadius(),
					"step" => 1,
					"default" => 0
				],
				[
					"type" => "toggle",
					"text" => Translation::get(Translation::UI_PRESET_CREATION_PERFECT),
					"default" => true
				],
				[
					"type" => "input",
					"text" => Translation::get(Translation::UI_PRESET_CREATION_BLOCKS),
					"placeholder" => "stone,stone_brick:1,2",
					"default" => "grass,98:2"
				],
				[
					"type" => "input",
					"text" => Translation::get(Translation::UI_PRESET_CREATION_OBSOLETE),
					"placeholder" => "stone,stone_brick:1,2",
					"default" => "grass,98:2"
				],
				[
					"type" => "input",
					"text" => Translation::get(Translation::UI_PRESET_CREATION_BIOME),
					"placeholder" => "plains",
					"default" => "plains"
				],
				[
					"type" => "input",
					"text" => Translation::get(Translation::UI_PRESET_CREATION_TREE),
					"placeholder" => "oak",
					"default" => "oak"
				]
			]
		];
	}

	public function handle(ModalFormResponsePacket $packet): bool {
		$data = json_decode($packet->formData, true);
		if($this->loader->getPresetManager()->isPreset($data[0])) {
			return false;
		}
		$processor = new PresetPropertyProcessor($this->player, $this->loader);
		foreach($data as $key => $value) {
			$processor->process($key, $value);
		}
		$this->navigate(WindowHandler::WINDOW_PRESET_MENU, $this->player, new WindowHandler());
		return true;
	}
}