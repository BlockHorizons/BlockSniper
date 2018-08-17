<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockSniper\ui\windows;

use BlockHorizons\BlockSniper\data\Translation;
use BlockHorizons\BlockSniper\ui\WindowHandler;
use pocketmine\network\mcpe\protocol\ModalFormResponsePacket;

class PresetSelectionWindow extends CustomWindow {

	public function process(): void {
		$this->data = [
			"type" => "form",
			"title" => Translation::get(Translation::UI_PRESET_SELECTION_TITLE),
			"content" => Translation::get(Translation::UI_PRESET_SELECTION_SUBTITLE),
			"buttons" => []
		];
		foreach($this->loader->getPresetManager()->getAllPresets() as $key => $name) {
			$this->data["buttons"][$key] = [
				"text" => $name,
				"image" => [
					"type" => "url",
					"data" => "http://www.clker.com/cliparts/k/T/w/u/G/S/transparent-yellow-checkmark-md.png"
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
		$preset = $this->loader->getPresetManager()->getPreset($presetName);
		$preset->apply($this->player, $this->loader);
		$this->navigate(WindowHandler::WINDOW_PRESET_MENU, $this->player, new WindowHandler());
		return true;
	}
}