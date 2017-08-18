<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockSniper\ui\windows;

use BlockHorizons\BlockSniper\data\Translation;
use BlockHorizons\BlockSniper\Loader;

class ConfigurationMenuWindow extends Window {

	public function process() {
		$s = $this->getLoader()->getSettings();
		$key = array_search($s->getLanguage(), Loader::getAvailableLanguages());
		$this->data = [
			"type" => "custom_form",
			"title" => (new Translation(Translation::UI_CONFIGURATION_MENU_TITLE))->getMessage(),
			"content" => [
				[
					"type" => "toggle",
					"text" => (new Translation(Translation::UI_CONFIGURATION_MENU_AUTO_UPDATE))->getMessage(),
					"default" => $s->updatesAutomatically()
				],
				[
					"type" => "dropdown",
					"text" => (new Translation(Translation::UI_CONFIGURATION_MENU_LANGUAGE))->getMessage(),
					"default" => ($key === false ? 0 : $key),
					"options" => Loader::getAvailableLanguages()
				],
				[
					"type" => "slider",
					"text" => (new Translation(Translation::UI_CONFIGURATION_MENU_BRUSH_ITEM))->getMessage(),
					"min" => 0,
					"step" => 1,
					"max" => 511,
					"default" => $s->getBrushItem()
				],
				[
					"type" => "slider",
					"text" => (new Translation(Translation::UI_CONFIGURATION_MENU_MAX_BRUSH_SIZE))->getMessage(),
					"min" => 0,
					"step" => 1,
					"max" => 60,
					"default" => $s->getMaxRadius()
				],
				[
					"type" => "slider",
					"text" => (new Translation(Translation::UI_CONFIGURATION_MENU_MIN_ASYNC_SIZE))->getMessage(),
					"min" => 10,
					"step" => 1,
					"max" => 25,
					"default" => $s->getMinimumAsynchronousSize()
				],
				[
					"type" => "slider",
					"text" => (new Translation(Translation::UI_CONFIGURATION_MENU_MAX_REVERTS))->getMessage(),
					"min" => 0,
					"step" => 1,
					"max" => 40,
					"default" => $s->getMaxUndoStores()
				],
				[
					"type" => "toggle",
					"text" => (new Translation(Translation::UI_CONFIGURATION_MENU_RESET_DECREMENT_BRUSH))->getMessage(),
					"default" => $s->resetDecrementBrush()
				],
				[
					"type" => "toggle",
					"text" => (new Translation(Translation::UI_CONFIGURATION_MENU_SAVE_BRUSH))->getMessage(),
					"default" => $s->saveBrushProperties()
				],
				[
					"type" => "toggle",
					"text" => (new Translation(Translation::UI_CONFIGURATION_MENU_DROP_PLANTS))->getMessage(),
					"default" => $s->dropLeafblowerPlants()
				],
				[
					"type" => "toggle",
					"text" => (new Translation(Translation::UI_CONFIGURATION_MENU_AUTO_GUI))->getMessage(),
					"default" => $s->openGuiAutomatically()
				],
				[
					"type" => "toggle",
					"text" => (new Translation(Translation::UI_CONFIGURATION_MENU_AUTO_RELOAD))->getMessage(),
					"default" => false
				]
			]
		];
	}
}