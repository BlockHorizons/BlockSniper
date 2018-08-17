<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockSniper\ui\windows;

use BlockHorizons\BlockSniper\data\Translation;
use BlockHorizons\BlockSniper\Loader;
use pocketmine\item\Item;
use pocketmine\Player;

class ConfigurationMenuWindow extends CustomWindow {

	public function __construct(Loader $loader, Player $requester) {
		parent::__construct($this->t(Translation::UI_CONFIGURATION_MENU_TITLE));
		$c = $loader->config;

		$this->addDropdown($this->t(Translation::UI_CONFIGURATION_MENU_LANGUAGE), Loader::getAvailableLanguages(), array_search($c->MessageLanguage, Loader::getAvailableLanguages()), function(Player $player, int $value) use ($c) {
			$c->MessageLanguage = Loader::getAvailableLanguages()[$value];
		});
		$this->addInput($this->t(Translation::UI_CONFIGURATION_MENU_BRUSH_ITEM), $c->BrushItem->ItemID . ":" . $c->BrushItem->ItemData, "396:0", function(Player $player, string $value) use ($c) {
			$item = Item::fromString($value);
			$c->BrushItem->ItemID = $item->getId();
			$c->BrushItem->ItemData = $item->getDamage();
		});
		$this->addSlider($this->t(Translation::UI_CONFIGURATION_MENU_MAX_BRUSH_SIZE), 0, 100, 1, $c->MaximumSize, function(Player $player, float $value) use ($c) {
			$c->MaximumSize = (int) $value;
		});
		$this->addSlider($this->t(Translation::UI_CONFIGURATION_MENU_MIN_ASYNC_SIZE), 10, 25, 1, $c->AsynchronousOperationSize, function(Player $player, float $value) use ($c) {
			$c->AsynchronousOperationSize = (int) $value;
		});
		$this->addSlider($this->t(Translation::UI_CONFIGURATION_MENU_MAX_REVERTS), 0, 40, 1, $c->MaximumRevertStores, function(Player $player, float $value) use ($c) {
			$c->MaximumRevertStores = (int) $value;
		});
		$this->addToggle($this->t(Translation::UI_CONFIGURATION_MENU_RESET_DECREMENT_BRUSH), $c->ResetDecrementBrush, function(Player $player, bool $value) use ($c) {
			$c->ResetDecrementBrush = $value;
		});
		$this->addToggle($this->t(Translation::UI_CONFIGURATION_MENU_SAVE_BRUSH), $c->SaveBrushProperties, function(Player $player, bool $value) use ($c) {
			$c->SaveBrushProperties = $value;
		});
		$this->addToggle($this->t(Translation::UI_CONFIGURATION_MENU_DROP_PLANTS), $c->DropLeafBlowerPlants, function(Player $player, bool $value) use ($c) {
			$c->DropLeafBlowerPlants = $value;
		});
		$this->addToggle($this->t(Translation::UI_CONFIGURATION_MENU_AUTO_GUI), $c->OpenGUIAutomatically, function(Player $player, bool $value) use ($c) {
			$c->OpenGUIAutomatically = $value;
		});
		$this->addToggle($this->t(Translation::UI_CONFIGURATION_MENU_MYPLOT_SUPPORT), $c->MyPlotSupport, function(Player $player, bool $value) use ($c) {
			$c->MyPlotSupport = $value;
		});
		$this->addToggle($this->t(Translation::UI_CONFIGURATION_MENU_AUTO_RELOAD), false, function(Player $player, bool $value) use ($c, $loader) {
			if($value) {
				$loader->reload();
			}
		});
	}
}