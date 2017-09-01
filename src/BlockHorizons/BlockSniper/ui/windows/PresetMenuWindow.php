<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockSniper\ui\windows;

use BlockHorizons\BlockSniper\data\Translation;

class PresetMenuWindow extends Window {

	public function process(): void {
		$this->data = [
			"type" => "form",
			"title" => (new Translation(Translation::UI_PRESET_MENU_TITLE))->getMessage(),
			"content" => (new Translation(Translation::UI_PRESET_MENU_SUBTITLE))->getMessage(),
			"buttons" => [
				[
					"text" => (new Translation(Translation::UI_PRESET_MENU_CREATE))->getMessage(),
					"image" => [
						"type" => "url",
						"data" => "http://www.clker.com/cliparts/H/D/e/R/O/P/green-plus-sign-hi.png"
					]
				],
				[
					"text" => (new Translation(Translation::UI_PRESET_MENU_DELETE))->getMessage(),
					"image" => [
						"type" => "url",
						"data" => "http://www.pngmart.com/files/3/Red-Cross-Transparent-PNG.png"
					]
				],
				[
					"text" => (new Translation(Translation::UI_PRESET_MENU_SELECT))->getMessage(),
					"image" => [
						"type" => "url",
						"data" => "http://www.clker.com/cliparts/k/T/w/u/G/S/transparent-yellow-checkmark-md.png"
					]
				],
				[
					"text" => (new Translation(Translation::UI_PRESET_MENU_LIST))->getMessage(),
					"image" => [
						"type" => "url",
						"data" => "http://www.iconsdb.com/icons/preview/guacamole-green/list-xxl.png"
					]
				],
				[
					"text" => (new Translation(Translation::UI_PRESET_MENU_EXIT))->getMessage(),
					"image" => [
						"type" => "url",
						"data" => "http://www.pngmart.com/files/3/Red-Cross-Transparent-PNG.png"
					]
				]
			]
		];
	}
}