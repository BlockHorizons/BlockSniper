<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\ui\window;

use BlockHorizons\BlockSniper\changelog\Changelog;
use BlockHorizons\BlockSniper\data\Translation;
use BlockHorizons\BlockSniper\Loader;
use BlockHorizons\BlockSniper\ui\form\MenuForm;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

class ChangeLogMenu extends MenuForm{

	public function __construct(){
		parent::__construct($this->t(Translation::UI_CHANGELOG_TITLE), $this->t(Translation::UI_CHANGELOG_SUBTITLE));
		foreach(Changelog::$changeLogs as $version => $changeLog){
			$text = "v$version";
			if($version === Loader::VERSION){
				$text = TextFormat::YELLOW . TextFormat::BOLD . ">> " . TextFormat::RESET . TextFormat::DARK_AQUA . $text . TextFormat::BOLD . TextFormat::YELLOW . " <<";
			}
			$this->addOption($text, "", "", function(Player $player) use ($changeLog){
				$this->setResponseForm($changeLog->toForm());
			}
			);
		}
	}
}