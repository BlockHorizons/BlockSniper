<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockSniper\ui\windows;

class PresetMenuWindow extends Window {

	const ID = 2;

	public function process() {
		$this->data = [
			"type" => "form",
			"title" => "Preset Menu",
			"content" => "Click a button to continue.",
			"buttons" => [
				[
					"text" => "Create",
					"image" => [
						"type" => "url",
						"data" => "http://www.clker.com/cliparts/H/D/e/R/O/P/green-plus-sign-hi.png"
					]
				],
				[
					"text" => "Delete",
					"image" => [
						"type" => "url",
						"data" => "http://www.pngmart.com/files/3/Red-Cross-Transparent-PNG.png"
					]
				],
				[
					"text" => "Select",
					"image" => [
						"type" => "url",
						"data" => "http://www.clker.com/cliparts/k/T/w/u/G/S/transparent-yellow-checkmark-md.png"
					]
				],
				[
					"text" => "List",
					"image" => [
						"type" => "url",
						"data" => "http://www.iconsdb.com/icons/preview/guacamole-green/list-xxl.png"
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