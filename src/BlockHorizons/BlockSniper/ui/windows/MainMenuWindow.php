<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockSniper\ui\windows;

use BlockHorizons\BlockSniper\data\Translation;
use BlockHorizons\BlockSniper\ui\WindowHandler;
use pocketmine\network\mcpe\protocol\ModalFormRequestPacket;
use pocketmine\network\mcpe\protocol\ModalFormResponsePacket;

class MainMenuWindow extends Window {

	public function process(): void {
		$this->data = [
			"type" => "form",
			"title" => (new Translation(Translation::UI_MAIN_MENU_TITLE))->getMessage(),
			"content" => (new Translation(Translation::UI_MAIN_MENU_SUBTITLE))->getMessage(),
			"buttons" => [
				[
					"text" => (new Translation(Translation::UI_MAIN_MENU_BRUSH))->getMessage(),
					"image" => [
						"type" => "url",
						"data" => "https://maxcdn.icons8.com/Share/icon/DIY//paint_brush1600.png"
					]
				],
				[
					"text" => (new Translation(Translation::UI_MAIN_MENU_PRESETS))->getMessage(),
					"image" => [
						"type" => "url",
						"data" => "http://www.sidecarpost.com/wp-content/uploads/2014/03/Icon-BaselinePreset-100x100.png"
					]
				],
				[
					"text" => (new Translation(Translation::UI_MAIN_MENU_EXIT))->getMessage(),
					"image" => [
						"type" => "url",
						"data" => "http://www.pngmart.com/files/3/Red-Cross-Transparent-PNG.png"
					]
				]
			]
		];
		if($this->getPlayer()->hasPermission("blocksniper.configuration")) {
			$this->data[3] = $this->data[2];
			$this->data[2] = [
				"text" => (new Translation(Translation::UI_MAIN_MENU_CONFIG))->getMessage(),
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
		$windowHandler = new WindowHandler();
		$packet = new ModalFormRequestPacket();
		$packet->formId = $windowHandler->getWindowIdFor($index);
		$packet->formData = $windowHandler->getWindowJson($index, $this->loader, $this->player);
		$this->player->dataPacket($packet);
		return true;
	}
}