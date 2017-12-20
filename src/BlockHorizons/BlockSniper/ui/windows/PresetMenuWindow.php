<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockSniper\ui\windows;

use BlockHorizons\BlockSniper\data\Translation;
use BlockHorizons\BlockSniper\ui\WindowHandler;
use pocketmine\network\mcpe\protocol\ModalFormRequestPacket;
use pocketmine\network\mcpe\protocol\ModalFormResponsePacket;

class PresetMenuWindow extends Window {

	public function process(): void {
		$this->data = [
			"type" => "form",
			"title" => Translation::get(Translation::UI_PRESET_MENU_TITLE),
			"content" => Translation::get(Translation::UI_PRESET_MENU_SUBTITLE),
			"buttons" => [
				[
					"text" => Translation::get(Translation::UI_PRESET_MENU_CREATE),
					"image" => [
						"type" => "url",
						"data" => "http://www.clker.com/cliparts/H/D/e/R/O/P/green-plus-sign-hi.png"
					]
				],
				[
					"text" => Translation::get(Translation::UI_PRESET_MENU_DELETE),
					"image" => [
						"type" => "url",
						"data" => "http://www.pngmart.com/files/3/Red-Cross-Transparent-PNG.png"
					]
				],
				[
					"text" => Translation::get(Translation::UI_PRESET_MENU_SELECT),
					"image" => [
						"type" => "url",
						"data" => "http://www.clker.com/cliparts/k/T/w/u/G/S/transparent-yellow-checkmark-md.png"
					]
				],
				[
					"text" => Translation::get(Translation::UI_PRESET_MENU_LIST),
					"image" => [
						"type" => "url",
						"data" => "http://www.iconsdb.com/icons/preview/guacamole-green/list-xxl.png"
					]
				],
				[
					"text" => Translation::get(Translation::UI_PRESET_MENU_EXIT),
					"image" => [
						"type" => "url",
						"data" => "http://www.pngmart.com/files/3/Red-Cross-Transparent-PNG.png"
					]
				]
			]
		];
	}

	public function handle(ModalFormResponsePacket $packet): bool {
		$index = (int) $packet->formData + 5;
		$windowHandler = new WindowHandler();
		if($index === 9) {
			$this->navigate(WindowHandler::WINDOW_MAIN_MENU, $this->player, $windowHandler);
			return false;
		}
		$packet = new ModalFormRequestPacket();
		$packet->formId = $windowHandler->getWindowIdFor($index);
		$packet->formData = $windowHandler->getWindowJson($index, $this->loader, $this->player);
		$this->player->dataPacket($packet);
		return true;
	}
}