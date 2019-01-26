<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\ui\windows;

use BlockHorizons\BlockSniper\brush\BrushProperties;
use BlockHorizons\BlockSniper\brush\registration\ShapeRegistration;
use BlockHorizons\BlockSniper\brush\registration\TypeRegistration;
use BlockHorizons\BlockSniper\data\Translation;
use BlockHorizons\BlockSniper\Loader;
use BlockHorizons\BlockSniper\presets\Preset;
use BlockHorizons\BlockSniper\sessions\SessionManager;
use pocketmine\Player;

class PresetCreationWindow extends CustomWindow{

	/** @var Preset */
	public $preset;

	public function __construct(Loader $loader, Player $requester){
		parent::__construct($this->t(Translation::UI_PRESET_CREATION_TITLE));
		$this->preset = new Preset("", new BrushProperties());
		$b = SessionManager::getPlayerSession($requester)->getBrush();

		$this->addInput($this->t(Translation::UI_PRESET_CREATION_NAME), "New Preset", $this->t(Translation::UI_PRESET_CREATION_NAME), function(Player $player, string $value){
			$this->preset->name = $value;
		});
		$this->addDropdown($this->t(Translation::UI_BRUSH_MENU_MODE_DESCRIPTION), $this->processModes(), $b->mode, function(Player $player, int $value) {
			$this->preset->properties->mode = $value;
		});
		$this->addDropdown($this->t(Translation::UI_BRUSH_MENU_SHAPE), $this->processShapes($requester), $b->getShape()::ID, function(Player $player, int $value){
			$this->preset->properties->shape = ShapeRegistration::getShapeById($value);
		});
		$this->addDropdown($this->t(Translation::UI_BRUSH_MENU_TYPE), $this->processTypes($requester), $b->getType()::ID, function(Player $player, int $value) use($loader) {
			$this->preset->properties->type = TypeRegistration::getTypeById($value);
			$loader->getPresetManager()->addPreset($this->preset);

			// Note to future readers: This response form is explicitly set after all preset properties have been
			// changed, as we want to send the requester back to the main menu if they press the cross.
			$form = new PresetPropertiesWindow($loader, $player, $this->preset);
			if($form->elementCount() !== 0){
				$this->setResponseForm($form);
			}
		});
		$this->setResponseForm(new PresetMenuWindow($loader, $requester));
	}
}