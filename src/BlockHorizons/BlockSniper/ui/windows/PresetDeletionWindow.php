<?php

namespace BlockHorizons\BlockSniper\ui\windows;

class PresetDeletionWindow extends Window {

	const ID = 6;

	public function process() {
		$presets = $this->getLoader()->getPresetManager()->getAllPresets();
		$this->data = [
			"type" => "form",
			"title" => "Preset Deletion Menu",
			"content" => "Select a preset to delete.",
			"buttons" => []
		];
		foreach($presets as $key => $name) {
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