<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockSniper\ui\windows;

use BlockHorizons\BlockSniper\data\Translation;
use BlockHorizons\BlockSniper\ui\WindowHandler;
use pocketmine\network\mcpe\protocol\ModalFormResponsePacket;

class MainMenuWindow extends Window {

	public function process(): void {
		$this->data = [
			"type" => "form",
			"title" => Translation::get(Translation::UI_MAIN_MENU_TITLE),
			"content" => Translation::get(Translation::UI_MAIN_MENU_SUBTITLE),
			"buttons" => [
				[
					"text" => Translation::get(Translation::UI_MAIN_MENU_BRUSH),
					"image" => [
						"type" => "url",
						"data" => "https://maxcdn.icons8.com/Share/icon/DIY//paint_brush1600.png"
					]
				],
				[
					"text" => Translation::get(Translation::UI_MAIN_MENU_PRESETS),
					"image" => [
						"type" => "url",
						"data" => "http://www.sidecarpost.com/wp-content/uploads/2014/03/Icon-BaselinePreset-100x100.png"
					]
				],
				[
					"text" => Translation::get(Translation::UI_MAIN_MENU_EXIT),
					"image" => [
						"type" => "url",
						"data" => "http://www.pngmart.com/files/3/Red-Cross-Transparent-PNG.png"
					]
				]
			]
		];
		if($this->getPlayer()->hasPermission("blocksniper.configuration")) {
			$this->data["buttons"][3] = $this->data["buttons"][2];
			$this->data["buttons"][2] = [
				"text" => Translation::get(Translation::UI_MAIN_MENU_CONFIG),
				"image" => [
					"type" => "url",
					"data" => "http://icons.iconarchive.com/icons/dtafalonso/android-l/512/Settings-L-icon.png"
				]
			];
		}
	}

	public function handle(ModalFormResponsePacket $packet): bool {
		$index = (int) $packet->formData + 1;
		if($index === 4) {
			return false;
		}
		$this->navigate($index, $this->player, new WindowHandler());
		return true;
	}
}