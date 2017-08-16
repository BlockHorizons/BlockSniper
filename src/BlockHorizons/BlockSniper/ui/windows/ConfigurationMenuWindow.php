<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockSniper\ui\windows;

use BlockHorizons\BlockSniper\Loader;

class ConfigurationMenuWindow extends Window {

	public function process() {
		$s = $this->getLoader()->getSettings();
		$key = array_search($s->getLanguage(), Loader::getAvailableLanguages());
		$this->data = [
			"type" => "custom_form",
			"title" => "BlockSniper Configuration",
			"content" => [
				[
					"type" => "toggle",
					"text" => "Automatically update the configuration file when a new version is found.",
					"default" => $s->updatesAutomatically()
				],
				[
					"type" => "dropdown",
					"text" => "Language used for sending BlockSniper related messages.",
					"default" => ($key === false ? 0 : $key),
					"options" => Loader::getAvailableLanguages()
				],
				[
					"type" => "slider",
					"text" => "Item ID of the item that is used to brush",
					"min" => 0,
					"step" => 1,
					"max" => 511,
					"default" => $s->getBrushItem()
				],
				[
					"type" => "slider",
					"text" => "Maximum brush size limit",
					"min" => 0,
					"step" => 1,
					"max" => 60,
					"default" => $s->getMaxRadius()
				],
				[
					"type" => "slider",
					"text" => "Asynchronous brush size limit. If brush size is bigger than this, operations get executed asynchronously",
					"min" => 10,
					"step" => 1,
					"max" => 25,
					"default" => $s->getMinimumAsynchronousSize()
				],
				[
					"type" => "slider",
					"text" => "Maximum amount of Undo/Redo stores to save. Older ones get cleaned automatically",
					"min" => 0,
					"step" => 1,
					"max" => 40,
					"default" => $s->getMaxUndoStores()
				],
				[
					"type" => "toggle",
					"text" => "Automatically reset decrementing brush to the initial starting size.",
					"default" => $s->resetDecrementBrush()
				],
				[
					"type" => "toggle",
					"text" => "Save brush properties of players on server restart.",
					"default" => $s->saveBrushProperties()
				],
				[
					"type" => "toggle",
					"text" => "Drop plant items destroyed/blown away by the LeafBlower type.",
					"default" => $s->dropLeafblowerPlants()
				],
				[
					"type" => "toggle",
					"text" => "Automatically open the GUI when switching hotbar slot to one containing the brush item.",
					"default" => $s->openGuiAutomatically()
				],
				[
					"type" => "toggle",
					"text" => "Turning this toggle will automatically reload the configuration when closing this window.",
					"default" => false
				]
			]
		];
	}
}