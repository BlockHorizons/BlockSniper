<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\ui\windows;

use BlockHorizons\BlockSniper\data\Translation;
use BlockHorizons\BlockSniper\Loader;
use BlockHorizons\BlockSniper\ui\forms\MenuForm;
use pocketmine\Player;

class PresetSelectionWindow extends MenuForm{

	private const SELECTION_ICON = "http://www.clker.com/cliparts/k/T/w/u/G/S/transparent-yellow-checkmark-md.png";

	public function __construct(Loader $loader, Player $requester){
		parent::__construct($this->t(Translation::UI_PRESET_SELECTION_TITLE), $this->t(Translation::UI_PRESET_SELECTION_SUBTITLE));

		foreach($loader->getPresetManager()->getAllPresets() as $preset){
			$this->addOption($preset->name, self::SELECTION_ICON, "url", function(Player $player, int $offset) use ($loader){
				$loader->getPresetManager()->getPreset($offset)->apply($player);
				$this->setResponseForm(null);
			});
		}
		$this->setResponseForm(new PresetMenuWindow($loader, $requester));
	}
}