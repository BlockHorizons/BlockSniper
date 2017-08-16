<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockSniper\ui\windows;

class PresetDeletionWindow extends Window {

	public function process() {
		$this->data = [
			"type" => "form",
			"title" => "Preset Deletion Menu",
			"content" => "Select a preset to delete.",
			"buttons" => []
		];
		foreach($this->getLoader()->getPresetManager()->getAllPresets() as $key => $name) {
			$this->data["buttons"][$key] = [
				"text" => $name,
				"image" => [
					"type" => "url",
					"data" => "http://www.pngmart.com/files/3/Red-Cross-Transparent-PNG.png"
				]
			];
		}
	}
}