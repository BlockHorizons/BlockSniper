<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockSniper\ui\windows;

use BlockHorizons\BlockSniper\data\Translation;

class PresetDeletionWindow extends Window {

	public function process() {
		$this->data = [
			"type" => "form",
			"title" => (new Translation(Translation::UI_PRESET_DELETION_TITLE))->getMessage(),
			"content" => (new Translation(Translation::UI_PRESET_DELETION_SUBTITLE))->getMessage(),
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