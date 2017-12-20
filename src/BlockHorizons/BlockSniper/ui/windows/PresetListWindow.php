<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockSniper\ui\windows;

use BlockHorizons\BlockSniper\data\Translation;
use BlockHorizons\BlockSniper\ui\WindowHandler;
use pocketmine\network\mcpe\protocol\ModalFormRequestPacket;
use pocketmine\network\mcpe\protocol\ModalFormResponsePacket;

class PresetListWindow extends Window {

	public function process(): void {
		$this->data = [
			"type" => "form",
			"title" => Translation::get(Translation::UI_PRESET_LIST_TITLE),
			"content" => Translation::get(Translation::UI_PRESET_LIST_SUBTITLE),
			"buttons" => []
		];
		foreach($this->getLoader()->getPresetManager()->getAllPresets() as $key => $name) {
			$this->data["buttons"][$key] = [
				"text" => $name,
				"image" => [
					"type" => "url",
					"data" => "http://www.iconsdb.com/icons/preview/guacamole-green/list-xxl.png"
				]
			];
		}
	}

	public function handle(ModalFormResponsePacket $packet): bool {
		$index = (int) $packet->formData;
		$presetName = "";
		foreach($this->loader->getPresetManager()->getAllPresets() as $key => $name) {
			if($key === $index) {
				$presetName = $name;
			}
		}
		$windowHandler = new WindowHandler();
		$preset = $this->loader->getPresetManager()->getPreset($presetName);
		/** @var PresetEditWindow $window */
		$window = $windowHandler->getWindow(WindowHandler::WINDOW_PRESET_EDIT_MENU, $this->loader, $this->player);
		$window->setPreset($preset);
		$window->process();

		$packet = new ModalFormRequestPacket();
		$packet->formId = $windowHandler->getWindowIdFor(WindowHandler::WINDOW_PRESET_EDIT_MENU);
		$packet->formData = $window->getJson();
		$this->player->dataPacket($packet);
		return true;
	}
}