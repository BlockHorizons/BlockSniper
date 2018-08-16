<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockSniper\ui\windows;

use BlockHorizons\BlockSniper\data\Translation;
use BlockHorizons\BlockSniper\ui\WindowHandler;
use pocketmine\network\mcpe\protocol\ModalFormResponsePacket;

class PresetDeletionWindow extends Window {

	public function process(): void {
		$this->data = [
			"type" => "form",
			"title" => Translation::get(Translation::UI_PRESET_DELETION_TITLE),
			"content" => Translation::get(Translation::UI_PRESET_DELETION_SUBTITLE),
			"buttons" => []
		];
		foreach($this->loader->getPresetManager()->getAllPresets() as $key => $name) {
			$this->data["buttons"][$key] = [
				"text" => $name,
				"image" => [
					"type" => "url",
					"data" => "http://www.pngmart.com/files/3/Red-Cross-Transparent-PNG.png"
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
		$this->loader->getPresetManager()->deletePreset($presetName);
		$this->navigate(WindowHandler::WINDOW_PRESET_MENU, $this->player, new WindowHandler());
		return true;
	}
}