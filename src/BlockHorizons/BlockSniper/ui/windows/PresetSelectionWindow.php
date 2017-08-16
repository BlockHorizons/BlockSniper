<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockSniper\ui\windows;

class PresetSelectionWindow extends Window {

	public function process() {
		$this->data = [
			"type" => "form",
			"title" => "Preset Selection Menu",
			"content" => "Select a preset to apply.",
			"buttons" => []
		];
		foreach($this->getLoader()->getPresetManager()->getAllPresets() as $key => $name) {
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