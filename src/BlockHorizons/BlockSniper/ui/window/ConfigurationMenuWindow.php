<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\ui\window;

use BlockHorizons\BlockSniper\data\Translation;
use BlockHorizons\BlockSniper\Loader;
use pocketmine\player\Player;
use pocketmine\utils\AssumptionFailedError;
use function array_search;

class ConfigurationMenuWindow extends CustomWindow{

	public function __construct(Loader $loader){
		parent::__construct($this->t(Translation::UI_CONFIGURATION_MENU_TITLE));
		$c = $loader->config;

		$selected = array_search($c->messageLanguage, Loader::getAvailableLanguages());
		if($selected === false){
			$selected = 0;
		}
		$this->addDropdown($this->t(Translation::UI_CONFIGURATION_MENU_LANGUAGE), Loader::getAvailableLanguages(), $selected, function(Player $player, int $value) use ($c){
			$c->messageLanguage = Loader::getAvailableLanguages()[$value];
		}
		);
		$this->addInput($this->t(Translation::UI_CONFIGURATION_MENU_BRUSH_ITEM), $c->brushItem->item, "golden_carrot", function(Player $player, string $value) use ($c){
			$c->brushItem->item = $value;
		}
		);
		$this->addInput($this->t(Translation::UI_CONFIGURATION_MENU_SELECTION_ITEM), $c->selectionItem->item, "glowstone_dust", function(Player $player, string $value) use ($c){
			$c->selectionItem->item = $value;
		}
		);
		$this->addSlider($this->t(Translation::UI_CONFIGURATION_MENU_MAX_BRUSH_SIZE), 0, 100, 1, $c->maxSize, function(Player $player, float $value) use ($c){
			$c->maxSize = (int) $value;
		}
		);
		$this->addSlider($this->t(Translation::UI_CONFIGURATION_MENU_MIN_ASYNC_SIZE), 10, 25, 1, $c->asyncOperationSize, function(Player $player, float $value) use ($c){
			$c->asyncOperationSize = (int) $value;
		}
		);
		$this->addSlider($this->t(Translation::UI_CONFIGURATION_MENU_MAX_REVERTS), 0, 40, 1, $c->maxRevertStores, function(Player $player, float $value) use ($c){
			$c->maxRevertStores = (int) $value;
		}
		);
		$this->addToggle($this->t(Translation::UI_CONFIGURATION_MENU_RESET_DECREMENT_BRUSH), $c->resetDecrementBrush, function(Player $player, bool $value) use ($c){
			$c->resetDecrementBrush = $value;
		}
		);
		$this->addToggle($this->t(Translation::UI_CONFIGURATION_MENU_SAVE_BRUSH), $c->saveBrushProperties, function(Player $player, bool $value) use ($c){
			$c->saveBrushProperties = $value;
		}
		);
		$this->addSlider($this->t(Translation::UI_CONFIGURATION_MENU_SESSION_TIMEOUT_TIME), 0, 30, 1, $c->sessionTimeoutTime, function(Player $player, float $value) use ($c){
			$c->sessionTimeoutTime = (int) $value;
		}
		);
		$this->addSlider($this->t(Translation::UI_CONFIGURATION_MENU_COOLDOWN), 0, 30, 0.5, $c->cooldownSeconds, function(Player $player, float $value) use ($c){
			$c->cooldownSeconds = $value;
		}
		);
		$this->addToggle($this->t(Translation::UI_CONFIGURATION_MENU_AUTO_GUI), $c->openGuiAutomatically, function(Player $player, bool $value) use ($c){
			$c->openGuiAutomatically = $value;
		}
		);
		$this->addToggle($this->t(Translation::UI_CONFIGURATION_MENU_MYPLOT_SUPPORT), $c->myPlotSupport, function(Player $player, bool $value) use ($c){
			$c->myPlotSupport = $value;
		}
		);
	}
}