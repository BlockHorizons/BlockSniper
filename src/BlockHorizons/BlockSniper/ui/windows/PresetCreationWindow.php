<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockSniper\ui\windows;

class PresetCreationWindow extends Window {

	const ID = 4;

	public function process() {
		$this->data = [
			"type" => "custom_form",
			"title" => "Preset Creation Menu",
			"content" => [
				[
					"type" => "input",
					"text" => "Preset Name",
					"default" => "New Preset",
					"placeholder" => "Preset Name"
				],
				[
					"type" => "slider",
					"text" => "Brush Size",
					"min" => 0,
					"max" => $this->getLoader()->getSettings()->getMaxRadius(),
					"step" => 1,
					"default" => 0
				],
				[
					"type" => "dropdown",
					"text" => "Brush Shape",
					"default" => 0,
					"options" => $this->processShapes()
				],
				[
					"type" => "dropdown",
					"text" => "Brush Type",
					"default" => 5,
					"options" => $this->processTypes()
				],
				[
					"type" => "toggle",
					"text" => "Hollow Brush",
					"default" => false
				],
				[
					"type" => "toggle",
					"text" => "Brush Decrement",
					"default" => false
				],
				[
					"type" => "slider",
					"text" => "Brush Height",
					"min" => 0,
					"max" => $this->getLoader()->getSettings()->getMaxRadius(),
					"step" => 1,
					"default" => 0
				],
				[
					"type" => "toggle",
					"text" => "Brush Shape Perfection",
					"default" => true
				],
				[
					"type" => "input",
					"text" => "Brush Blocks",
					"placeholder" => "stone,stone_brick:1,2",
					"default" => "grass,98:2"
				],
				[
					"type" => "input",
					"text" => "Obsolete Blocks",
					"placeholder" => "stone,stone_brick:1,2",
					"default" => "grass,98:2"
				],
				[
					"type" => "input",
					"text" => "Brush Biome",
					"placeholder" => "plains",
					"default" => "plains"
				],
				[
					"type" => "input",
					"text" => "Brush Tree",
					"placeholder" => "oak",
					"default" => "oak"
				]
			]
		];
	}
}