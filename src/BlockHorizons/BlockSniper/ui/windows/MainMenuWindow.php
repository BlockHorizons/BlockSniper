<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockSniper\ui\windows;

class MainMenuWindow extends Window {

	public function process() {
		$this->data = [
			"type" => "form",
			"title" => "BlockSniper Menu",
			"content" => "Click a button to continue.",
			"buttons" => [
				[
					"text" => "Brush",
					"image" => [
						"type" => "url",
						"data" => "https://maxcdn.icons8.com/Share/icon/DIY//paint_brush1600.png"
					]
				],
				[
					"text" => "Presets",
					"image" => [
						"type" => "url",
						"data" => "http://www.sidecarpost.com/wp-content/uploads/2014/03/Icon-BaselinePreset-100x100.png"
					]
				],
				[
					"text" => "Configuration",
					"image" => [
						"type" => "url",
						"data" => "http://icons.iconarchive.com/icons/dtafalonso/android-l/512/Settings-L-icon.png"
					]
				],
				[
					"text" => "Exit",
					"image" => [
						"type" => "url",
						"data" => "http://www.pngmart.com/files/3/Red-Cross-Transparent-PNG.png"
					]
				]
			]
		];
	}
}