<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\ui\windows;

use BlockHorizons\BlockSniper\data\Translation;
use BlockHorizons\BlockSniper\Loader;
use BlockHorizons\BlockSniper\ui\forms\MenuForm;
use pocketmine\Player;

class PresetListWindow extends MenuForm{

	private const LIST_ICON = "http://www.iconsdb.com/icons/preview/guacamole-green/list-xxl.png";

	public function __construct(Loader $loader, Player $requester){
		parent::__construct($this->t(Translation::UI_PRESET_LIST_TITLE), $this->t(Translation::UI_PRESET_LIST_SUBTITLE));

		foreach($loader->getPresetManager()->getAllPresets() as $preset){
			$this->addOption($preset->name, self::LIST_ICON, "url", function(Player $player, int $offset) use ($loader){
				$this->setResponseForm(new PresetEditWindow($loader, $player, $loader->getPresetManager()->getPreset($offset)));
			});
		}

		$this->setResponseForm(new PresetMenuWindow($loader, $requester));
	}
}