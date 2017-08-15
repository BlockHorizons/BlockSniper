<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockSniper\ui\windows;

use BlockHorizons\BlockSniper\brush\BaseShape;
use BlockHorizons\BlockSniper\brush\BaseType;

class PresetCreationWindow extends Window {

	const ID = 4;

	public function process() {
		$shapes = BaseShape::getShapes();
		foreach($shapes as $key => $shape) {
			if(!$this->getPlayer()->hasPermission("blocksniper.shape." . strtolower(str_replace(" ", "", $shape)))) {
				unset($shapes[$key]);
			}
		}
		$types = BaseType::getTypes();
		foreach($types as $key => $type) {
			if(!$this->getPlayer()->hasPermission("blocksniper.type." . strtolower(str_replace(" ", "", $type)))) {
				unset($types[$key]);
			}
		}
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
					"options" => $shapes
				],
				[
					"type" => "dropdown",
					"text" => "Brush Type",
					"default" => 5,
					"options" => $types
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