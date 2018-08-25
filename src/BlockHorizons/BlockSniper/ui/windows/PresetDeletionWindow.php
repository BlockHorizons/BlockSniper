<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\ui\windows;

use BlockHorizons\BlockSniper\data\Translation;
use BlockHorizons\BlockSniper\Loader;
use BlockHorizons\BlockSniper\ui\forms\MenuForm;
use pocketmine\Player;

class PresetDeletionWindow extends MenuForm{

	private const DELETION_ICON = "http://www.pngmart.com/files/3/Red-Cross-Transparent-PNG.png";

	public function __construct(Loader $loader, Player $requester){
		parent::__construct($this->t(Translation::UI_PRESET_DELETION_TITLE), $this->t(Translation::UI_PRESET_DELETION_SUBTITLE));

		foreach($loader->getPresetManager()->getAllPresets() as $preset){
			$this->addOption($preset->name, self::DELETION_ICON, "url", function(Player $player, int $offset) use ($loader){
				$loader->getPresetManager()->deletePreset($offset);
			});
		}
		$this->setResponseForm(new PresetMenuWindow($loader, $requester));
	}
}