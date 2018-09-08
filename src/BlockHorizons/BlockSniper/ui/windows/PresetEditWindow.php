<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\ui\windows;

use BlockHorizons\BlockSniper\brush\registration\ShapeRegistration;
use BlockHorizons\BlockSniper\brush\registration\TypeRegistration;
use BlockHorizons\BlockSniper\data\Translation;
use BlockHorizons\BlockSniper\Loader;
use BlockHorizons\BlockSniper\presets\Preset;
use pocketmine\Player;

class PresetEditWindow extends CustomWindow{

	/** @var Preset */
	private $preset;

	public function __construct(Loader $loader, Player $requester, Preset $preset){
		parent::__construct($this->t(Translation::UI_PRESET_EDIT_TITLE));
		$this->preset = $preset;

		$this->addInput($this->t(Translation::UI_PRESET_CREATION_NAME), $preset->name, $this->t(Translation::UI_PRESET_CREATION_NAME), function(Player $player, string $value){
			$this->preset->name = $value;
		});
		$this->addSlider($this->t(Translation::UI_PRESET_CREATION_SIZE), 0, $loader->config->maxSize, 1, $preset->properties->size, function(Player $player, float $value){
			$this->preset->properties->size = (int) $value;
		});
		$this->addDropdown($this->t(Translation::UI_PRESET_CREATION_SHAPE), $this->processShapes($requester), ($preset->properties->shape)::ID, function(Player $player, int $value){
			$this->preset->properties->shape = ShapeRegistration::getShapeById($value);
		});
		$this->addDropdown($this->t(Translation::UI_PRESET_CREATION_TYPE), $this->processTypes($requester), ($preset->properties->type)::ID, function(Player $player, int $value){
			$this->preset->properties->type = TypeRegistration::getTypeById($value);
		});
		$this->addToggle($this->t(Translation::UI_PRESET_CREATION_HOLLOW), $preset->properties->hollow, function(Player $player, bool $value){
			$this->preset->properties->hollow = $value;
		});
		$this->addToggle($this->t(Translation::UI_PRESET_CREATION_DECREMENT), $preset->properties->decrementing, function(Player $player, bool $value){
			$this->preset->properties->decrementing = $value;
		});
		$this->addSlider($this->t(Translation::UI_PRESET_CREATION_HEIGHT), 0, $loader->config->maxSize, 1, $preset->properties->height, function(Player $player, float $value){
			$this->preset->properties->height = (int) $value;
		});
		$this->addInput($this->t(Translation::UI_PRESET_CREATION_BLOCKS), $preset->properties->blocks, "stone,stone_brick:1,2", function(Player $player, string $value){
			$this->preset->properties->blocks = $value;
		});
		$this->addInput($this->t(Translation::UI_PRESET_CREATION_OBSOLETE), $preset->properties->obsolete, "stone,stone_brick:1,2", function(Player $player, string $value){
			$this->preset->properties->obsolete = $value;
		});
		$this->addInput($this->t(Translation::UI_PRESET_CREATION_BIOME), (string) $preset->properties->biomeId, "plains", function(Player $player, string $value){
			$this->preset->properties->biome = $this->preset->properties->parseBiomeId($value);
		});
		$this->setResponseForm(new PresetMenuWindow($loader, $requester));
	}
}