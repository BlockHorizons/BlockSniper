<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\ui\window;

use BlockHorizons\BlockSniper\data\Translation;
use BlockHorizons\BlockSniper\Loader;
use BlockHorizons\BlockSniper\session\SessionManager;
use BlockHorizons\BlockSniper\ui\form\MenuForm;
use pocketmine\player\Player;

class MainMenuWindow extends MenuForm{

	private const BRUSH_ICON = "https://maxcdn.icons8.com/Share/icon/DIY//paint_brush1600.png";
	private const CONFIG_ICON = "http://icons.iconarchive.com/icons/dtafalonso/android-l/512/Settings-L-icon.png";
	private const CHANGELOG_ICON = "http://icons.iconarchive.com/icons/papirus-team/papirus-mimetypes/256/text-x-changelog-icon.png";

	public function __construct(Loader $loader, Player $requester){
		parent::__construct($this->t(Translation::UI_MAIN_MENU_TITLE), $this->t(Translation::UI_MAIN_MENU_SUBTITLE));

		$this->addOption($this->t(Translation::UI_MAIN_MENU_BRUSH), self::BRUSH_ICON, "url", function(Player $player) use ($loader){
			$this->setResponseForm(new BrushMenuWindow($loader, $player, SessionManager::getPlayerSession($player)->getBrush()));
		}
		);
		$this->addOption($this->t(Translation::UI_CHANGELOG_NAME), self::CHANGELOG_ICON, "url", function(Player $player){
			$this->setResponseForm(new ChangeLogMenu());
		}
		);
		if($requester->hasPermission("blocksniper.configuration")){
			$this->addOption($this->t(Translation::UI_MAIN_MENU_CONFIG), self::CONFIG_ICON, "url", function(Player $player) use ($loader){
				$this->setResponseForm(new ConfigurationMenuWindow($loader));
			}
			);
		}
	}
}