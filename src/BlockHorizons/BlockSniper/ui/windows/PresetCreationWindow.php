<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\ui\windows;

use BlockHorizons\BlockSniper\brush\BrushProperties;
use BlockHorizons\BlockSniper\brush\registration\ShapeRegistration;
use BlockHorizons\BlockSniper\brush\registration\TypeRegistration;
use BlockHorizons\BlockSniper\brush\shapes\SphereShape;
use BlockHorizons\BlockSniper\brush\types\FillType;
use BlockHorizons\BlockSniper\data\Translation;
use BlockHorizons\BlockSniper\Loader;
use BlockHorizons\BlockSniper\presets\Preset;
use pocketmine\Player;

class PresetCreationWindow extends CustomWindow{

	/** @var Preset */
	private $preset;

	public function __construct(Loader $loader, Player $requester){
		parent::__construct($this->t(Translation::UI_PRESET_MENU_TITLE));
		$this->preset = new Preset("", new BrushProperties());

		$this->addInput($this->t(Translation::UI_PRESET_CREATION_NAME), "New Preset", $this->t(Translation::UI_PRESET_CREATION_NAME), function(Player $player, string $value){
			$this->preset->name = $value;
		});
		$this->addSlider($this->t(Translation::UI_PRESET_CREATION_SIZE), 0, $loader->config->maxSize, 1, 10, function(Player $player, float $value){
			$this->preset->properties->size = (int) $value;
		});
		$this->addDropdown($this->t(Translation::UI_PRESET_CREATION_SHAPE), $this->processShapes($requester), SphereShape::ID, function(Player $player, int $value){
			$this->preset->properties->shape = ShapeRegistration::getShapeById($value);
		});
		$this->addDropdown($this->t(Translation::UI_PRESET_CREATION_TYPE), $this->processTypes($requester), FillType::ID, function(Player $player, int $value){
			$this->preset->properties->type = TypeRegistration::getTypeById($value);
		});
		$this->addToggle($this->t(Translation::UI_PRESET_CREATION_HOLLOW), false, function(Player $player, bool $value){
			$this->preset->properties->hollow = $value;
		});
		$this->addToggle($this->t(Translation::UI_PRESET_CREATION_DECREMENT), false, function(Player $player, bool $value){
			$this->preset->properties->decrementing = $value;
		});
		$this->addSlider($this->t(Translation::UI_PRESET_CREATION_HEIGHT), 0, $loader->config->maxSize, 1, 10, function(Player $player, float $value){
			$this->preset->properties->height = (int) $value;
		});
		$this->addInput($this->t(Translation::UI_PRESET_CREATION_BLOCKS), "stone", "stone,stone_brick:1,2", function(Player $player, string $value){
			$this->preset->properties->blocks = $value;
		});
		$this->addInput($this->t(Translation::UI_PRESET_CREATION_OBSOLETE), "air", "stone,stone_brick:1,2", function(Player $player, string $value){
			$this->preset->properties->obsolete = $value;
		});
		$this->addInput($this->t(Translation::UI_PRESET_CREATION_BIOME), "plains", "plains", function(Player $player, string $value){
			$this->preset->properties->biome = $this->preset->properties->parseBiomeId($value);
			// Last element, so we finish the preset here and add it.
			if(!$loader->getPresetManager()->isPreset($this->preset->name)){
				$loader->getPresetManager()->addPreset($this->preset);
			}
		});
		$this->setResponseForm(new PresetMenuWindow($loader, $requester));
	}
}