<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\ui\windows;

use BlockHorizons\BlockSniper\data\Translation;
use BlockHorizons\BlockSniper\Loader;
use BlockHorizons\BlockSniper\ui\forms\MenuForm;
use pocketmine\Player;

class PresetMenuWindow extends MenuForm{

	private const CREATE_ICON = "http://www.clker.com/cliparts/H/D/e/R/O/P/green-plus-sign-hi.png";
	private const DELETE_ICON = "http://www.pngmart.com/files/3/Red-Cross-Transparent-PNG.png";
	private const SELECT_ICON = "http://www.clker.com/cliparts/k/T/w/u/G/S/transparent-yellow-checkmark-md.png";
	private const LIST_ICON = "http://www.iconsdb.com/icons/preview/guacamole-green/list-xxl.png";
	private const EXIT_ICON = self::DELETE_ICON;

	public function __construct(Loader $loader, Player $requester){
		parent::__construct($this->t(Translation::UI_PRESET_MENU_TITLE), $this->t(Translation::UI_PRESET_MENU_SUBTITLE));
		$this->setResponseForm(new MainMenuWindow($loader, $requester));

		$this->addOption($this->t(Translation::UI_PRESET_MENU_CREATE), self::CREATE_ICON, "url", function(Player $player) use ($loader){
			$this->setResponseForm(new PresetCreationWindow($loader, $player));
		});
		$this->addOption($this->t(Translation::UI_PRESET_MENU_DELETE), self::DELETE_ICON, "url", function(Player $player) use ($loader){
			$this->setResponseForm(new PresetDeletionWindow($loader, $player));
		});
		$this->addOption($this->t(Translation::UI_PRESET_MENU_SELECT), self::SELECT_ICON, "url", function(Player $player) use ($loader){
			$this->setResponseForm(new PresetSelectionWindow($loader, $player));
		});
		$this->addOption($this->t(Translation::UI_PRESET_MENU_LIST), self::LIST_ICON, "url", function(Player $player) use ($loader){
			$this->setResponseForm(new PresetListWindow($loader, $player));
		});
		$this->addOption($this->t(Translation::UI_PRESET_MENU_EXIT), self::EXIT_ICON, "url", function(Player $player) use ($loader){
			$this->setResponseForm(new MainMenuWindow($loader, $player));
		});
	}
}