<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\ui\windows;

use BlockHorizons\BlockSniper\changelog\ChangeLog;
use BlockHorizons\BlockSniper\data\Translation;
use BlockHorizons\BlockSniper\Loader;
use BlockHorizons\BlockSniper\ui\forms\MenuForm;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class ChangeLogMenu extends MenuForm{

	public function __construct(Player $requester){
		parent::__construct($this->t(Translation::UI_CHANGELOG_TITLE), $this->t(Translation::UI_CHANGELOG_SUBTITLE));
		foreach(ChangeLog::$changeLogs as $version => $changeLog){
			$text = "v$version";
			if($version === Loader::VERSION){
				$text = TextFormat::YELLOW . TextFormat::BOLD . ">> " . TextFormat::RESET . TextFormat::DARK_AQUA . $text . TextFormat::BOLD . TextFormat::YELLOW . " <<";
			}
			$this->addOption($text, "", "", function(Player $player) use($changeLog) {
				$this->setResponseForm($changeLog->toForm());
			});
		}
	}
}