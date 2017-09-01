<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockSniper\ui\windows;

use BlockHorizons\BlockSniper\data\Translation;

class PresetCreationWindow extends Window {

	public function process(): void {
		$this->data = [
			"type" => "custom_form",
			"title" => (new Translation(Translation::UI_PRESET_CREATION_TITLE))->getMessage(),
			"content" => [
				[
					"type" => "input",
					"text" => (new Translation(Translation::UI_PRESET_CREATION_NAME))->getMessage(),
					"default" => "New Preset",
					"placeholder" => (new Translation(Translation::UI_PRESET_CREATION_NAME))->getMessage()
				],
				[
					"type" => "slider",
					"text" => (new Translation(Translation::UI_PRESET_CREATION_SIZE))->getMessage(),
					"min" => 0,
					"max" => $this->getLoader()->getSettings()->getMaxRadius(),
					"step" => 1,
					"default" => 0
				],
				[
					"type" => "dropdown",
					"text" => (new Translation(Translation::UI_PRESET_CREATION_SHAPE))->getMessage(),
					"default" => 0,
					"options" => $this->processShapes()
				],
				[
					"type" => "dropdown",
					"text" => (new Translation(Translation::UI_PRESET_CREATION_TYPE))->getMessage(),
					"default" => 5,
					"options" => $this->processTypes()
				],
				[
					"type" => "toggle",
					"text" => (new Translation(Translation::UI_PRESET_CREATION_HOLLOW))->getMessage(),
					"default" => false
				],
				[
					"type" => "toggle",
					"text" => (new Translation(Translation::UI_PRESET_CREATION_DECREMENT))->getMessage(),
					"default" => false
				],
				[
					"type" => "slider",
					"text" => (new Translation(Translation::UI_PRESET_CREATION_HEIGHT))->getMessage(),
					"min" => 0,
					"max" => $this->getLoader()->getSettings()->getMaxRadius(),
					"step" => 1,
					"default" => 0
				],
				[
					"type" => "toggle",
					"text" => (new Translation(Translation::UI_PRESET_CREATION_PERFECT))->getMessage(),
					"default" => true
				],
				[
					"type" => "input",
					"text" => (new Translation(Translation::UI_PRESET_CREATION_BLOCKS))->getMessage(),
					"placeholder" => "stone,stone_brick:1,2",
					"default" => "grass,98:2"
				],
				[
					"type" => "input",
					"text" => (new Translation(Translation::UI_PRESET_CREATION_OBSOLETE))->getMessage(),
					"placeholder" => "stone,stone_brick:1,2",
					"default" => "grass,98:2"
				],
				[
					"type" => "input",
					"text" => (new Translation(Translation::UI_PRESET_CREATION_BIOME))->getMessage(),
					"placeholder" => "plains",
					"default" => "plains"
				],
				[
					"type" => "input",
					"text" => (new Translation(Translation::UI_PRESET_CREATION_TREE))->getMessage(),
					"placeholder" => "oak",
					"default" => "oak"
				]
			]
		];
	}
}