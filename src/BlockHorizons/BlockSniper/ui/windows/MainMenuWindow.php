<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\ui\windows;

use BlockHorizons\BlockSniper\data\Translation;
use BlockHorizons\BlockSniper\Loader;
use BlockHorizons\BlockSniper\ui\forms\MenuForm;
use pocketmine\Player;

class MainMenuWindow extends MenuForm{

	private const BRUSH_ICON = "https://maxcdn.icons8.com/Share/icon/DIY//paint_brush1600.png";
	private const PRESET_ICON = "http://www.sidecarpost.com/wp-content/uploads/2014/03/Icon-BaselinePreset-100x100.png";
	private const CONFIG_ICON = "http://icons.iconarchive.com/icons/dtafalonso/android-l/512/Settings-L-icon.png";

	public function __construct(Loader $loader, Player $requester){
		parent::__construct($this->t(Translation::UI_MAIN_MENU_TITLE), $this->t(Translation::UI_MAIN_MENU_SUBTITLE));

		$this->addOption($this->t(Translation::UI_MAIN_MENU_BRUSH), self::BRUSH_ICON, "url", function(Player $player) use ($loader){
			$this->setResponseForm(new BrushMenuWindow($loader, $player));
		});
		$this->addOption($this->t(Translation::UI_MAIN_MENU_PRESETS), self::PRESET_ICON, "url", function(Player $player) use ($loader){
			$this->setResponseForm(new PresetMenuWindow($loader, $player));
		});
		if($requester->hasPermission("blocksniper.configuration")){
			$this->addOption($this->t(Translation::UI_MAIN_MENU_CONFIG), self::CONFIG_ICON, "url", function(Player $player) use ($loader){
				$this->setResponseForm(new ConfigurationMenuWindow($loader, $player));
			});
		}
	}
}