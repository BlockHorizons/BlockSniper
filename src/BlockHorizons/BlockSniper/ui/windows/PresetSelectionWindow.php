<?php

namespace BlockHorizons\BlockSniper\ui\windows;

class PresetSelectionWindow extends Window {

	const ID = 5;

	public function process() {
		$presets = $this->getLoader()->getPresetManager()->getAllPresets();
		$this->data = [
			"type" => "form",
			"title" => "Preset Selection Menu",
			"content" => "Select a preset to apply.",
			"buttons" => []
		];
		foreach($presets as $key => $name) {
			$this->data["buttons"][$key] = [
				"text" => $name,
				"image" => [
					"type" => "url",
					"data" => "http://www.clker.com/cliparts/k/T/w/u/G/S/transparent-yellow-checkmark-md.png"
				]
			];
		}
	}
}