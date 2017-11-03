<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockSniper\ui\windows;

use BlockHorizons\BlockSniper\data\Translation;
use BlockHorizons\BlockSniper\Loader;
use BlockHorizons\BlockSniper\ui\WindowHandler;
use pocketmine\network\mcpe\protocol\ModalFormResponsePacket;

class ConfigurationMenuWindow extends Window {

	public function process(): void {
		$s = $this->getLoader()->getSettings();
		$key = array_search($s->getLanguage(), Loader::getAvailableLanguages());
		$this->data = [
			"type" => "custom_form",
			"title" => Translation::get(Translation::UI_CONFIGURATION_MENU_TITLE),
			"content" => [
				[
					"type" => "toggle",
					"text" => Translation::get(Translation::UI_CONFIGURATION_MENU_AUTO_UPDATE),
					"default" => $s->updatesAutomatically()
				],
				[
					"type" => "dropdown",
					"text" => Translation::get(Translation::UI_CONFIGURATION_MENU_LANGUAGE),
					"default" => $key === false ? 0 : $key,
					"options" => Loader::getAvailableLanguages()
				],
				[
					"type" => "slider",
					"text" => Translation::get(Translation::UI_CONFIGURATION_MENU_BRUSH_ITEM),
					"min" => 0,
					"step" => 1,
					"max" => 511,
					"default" => $s->getBrushItem()
				],
				[
					"type" => "slider",
					"text" => Translation::get(Translation::UI_CONFIGURATION_MENU_MAX_BRUSH_SIZE),
					"min" => 0,
					"step" => 1,
					"max" => 60,
					"default" => $s->getMaxRadius()
				],
				[
					"type" => "slider",
					"text" => Translation::get(Translation::UI_CONFIGURATION_MENU_MIN_ASYNC_SIZE),
					"min" => 10,
					"step" => 1,
					"max" => 25,
					"default" => $s->getMinimumAsynchronousSize()
				],
				[
					"type" => "slider",
					"text" => Translation::get(Translation::UI_CONFIGURATION_MENU_MAX_REVERTS),
					"min" => 0,
					"step" => 1,
					"max" => 40,
					"default" => $s->getMaxUndoStores()
				],
				[
					"type" => "toggle",
					"text" => Translation::get(Translation::UI_CONFIGURATION_MENU_RESET_DECREMENT_BRUSH),
					"default" => $s->resetDecrementBrush()
				],
				[
					"type" => "toggle",
					"text" => Translation::get(Translation::UI_CONFIGURATION_MENU_SAVE_BRUSH),
					"default" => $s->saveBrushProperties()
				],
				[
					"type" => "toggle",
					"text" => Translation::get(Translation::UI_CONFIGURATION_MENU_DROP_PLANTS),
					"default" => $s->dropLeafblowerPlants()
				],
				[
					"type" => "toggle",
					"text" => Translation::get(Translation::UI_CONFIGURATION_MENU_AUTO_GUI),
					"default" => $s->openGuiAutomatically()
				],
				[
					"type" => "toggle",
					"text" => Translation::get(Translation::UI_CONFIGURATION_MENU_MYPLOT_SUPPORT),
					"default" => $s->hasMyPlotSupport()
				],
				[
					"type" => "toggle",
					"text" => Translation::get(Translation::UI_CONFIGURATION_MENU_AUTO_RELOAD),
					"default" => false
				]
			]
		];
	}

	public function handle(ModalFormResponsePacket $packet): bool {
		$data = json_decode($packet->formData, true);
		foreach($data as $key => $value) {
			if($key === 1) {
				$value = Loader::getAvailableLanguages()[$value];
			}
			$this->getLoader()->getSettings()->set($key, $value);
		}
		if($data[11] === true) {
			$this->loader->reload();
		}
		$windowHandler = new WindowHandler();
		$this->navigate(WindowHandler::WINDOW_MAIN_MENU, $this->player, $windowHandler);
		return true;
	}
}