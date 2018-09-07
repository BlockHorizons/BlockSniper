<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\ui\windows;

use BlockHorizons\BlockSniper\data\Translation;
use BlockHorizons\BlockSniper\Loader;
use pocketmine\item\Item;
use pocketmine\Player;

class ConfigurationMenuWindow extends CustomWindow{

	public function __construct(Loader $loader, Player $requester){
		parent::__construct($this->t(Translation::UI_CONFIGURATION_MENU_TITLE));
		$c = $loader->config;

		$this->addDropdown($this->t(Translation::UI_CONFIGURATION_MENU_LANGUAGE), Loader::getAvailableLanguages(), array_search($c->messageLanguage, Loader::getAvailableLanguages()), function(Player $player, int $value) use ($c){
			$c->messageLanguage = Loader::getAvailableLanguages()[$value];
		});
		$this->addInput($this->t(Translation::UI_CONFIGURATION_MENU_BRUSH_ITEM), $c->brushItem->itemId . ":" . $c->brushItem->itemData, "396:0", function(Player $player, string $value) use ($c){
			$item = Item::fromString($value);
			$c->brushItem->itemId = $item->getId();
			$c->brushItem->itemData = $item->getDamage();
		});
		$this->addSlider($this->t(Translation::UI_CONFIGURATION_MENU_MAX_BRUSH_SIZE), 0, 100, 1, $c->maxSize, function(Player $player, float $value) use ($c){
			$c->maxSize = (int) $value;
		});
		$this->addSlider($this->t(Translation::UI_CONFIGURATION_MENU_MIN_ASYNC_SIZE), 10, 25, 1, $c->asyncOperationSize, function(Player $player, float $value) use ($c){
			$c->asyncOperationSize = (int) $value;
		});
		$this->addSlider($this->t(Translation::UI_CONFIGURATION_MENU_MAX_REVERTS), 0, 40, 1, $c->maxRevertStores, function(Player $player, float $value) use ($c){
			$c->maxRevertStores = (int) $value;
		});
		$this->addToggle($this->t(Translation::UI_CONFIGURATION_MENU_RESET_DECREMENT_BRUSH), $c->resetDecrementBrush, function(Player $player, bool $value) use ($c){
			$c->resetDecrementBrush = $value;
		});
		$this->addToggle($this->t(Translation::UI_CONFIGURATION_MENU_SAVE_BRUSH), $c->saveBrushProperties, function(Player $player, bool $value) use ($c){
			$c->saveBrushProperties = $value;
		});
		$this->addToggle($this->t(Translation::UI_CONFIGURATION_MENU_DROP_PLANTS), $c->dropLeafBlowerPlants, function(Player $player, bool $value) use ($c){
			$c->dropLeafBlowerPlants = $value;
		});
		$this->addSlider($this->t(Translation::UI_CONFIGURATION_MENU_SESSION_TIMEOUT_TIME), 0, 30, 1, $c->sessionTimeoutTime, function(Player $player, float $value) use ($c){
			$c->sessionTimeoutTime = (int) $value;
		});
		$this->addToggle($this->t(Translation::UI_CONFIGURATION_MENU_AUTO_GUI), $c->openGuiAutomatically, function(Player $player, bool $value) use ($c){
			$c->openGuiAutomatically = $value;
		});
		$this->addToggle($this->t(Translation::UI_CONFIGURATION_MENU_MYPLOT_SUPPORT), $c->myPlotSupport, function(Player $player, bool $value) use ($c){
			$c->myPlotSupport = $value;
		});
		$this->addToggle($this->t(Translation::UI_CONFIGURATION_MENU_AUTO_RELOAD), false, function(Player $player, bool $value) use ($c, $loader){
			if($value){
				$loader->reload();
			}
		});
	}
}