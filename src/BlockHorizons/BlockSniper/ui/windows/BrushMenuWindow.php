<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockSniper\ui\windows;

use BlockHorizons\BlockSniper\brush\registration\ShapeRegistration;
use BlockHorizons\BlockSniper\brush\registration\TypeRegistration;
use BlockHorizons\BlockSniper\data\Translation;
use BlockHorizons\BlockSniper\Loader;
use BlockHorizons\BlockSniper\sessions\SessionManager;
use pocketmine\Player;

class BrushMenuWindow extends CustomWindow {

	public function __construct(Loader $loader, Player $requester) {
		parent::__construct($this->t(Translation::UI_BRUSH_MENU_TITLE));

		$b = SessionManager::getPlayerSession($requester)->getBrush();
		$this->addSlider($this->t(Translation::UI_BRUSH_MENU_SIZE), 0, $loader->config->MaximumSize, 1, $b->size, function(Player $player, float $value) use ($b) {
			$b->size = (int) $value;
		});
		$this->addDropdown($this->t(Translation::UI_BRUSH_MENU_SHAPE), $this->processShapes($requester), $b->getShape()::ID, function(Player $player, int $value) use ($b) {
			$b->shape = ShapeRegistration::getShapeById($value);
		});
		$this->addDropdown($this->t(Translation::UI_BRUSH_MENU_TYPE), $this->processTypes($requester), $b->getType()::ID, function(Player $player, int $value) use ($b) {
			$b->type = TypeRegistration::getTypeById($value);
		});
		$this->addToggle($this->t(Translation::UI_BRUSH_MENU_HOLLOW), $b->hollow, function(Player $player, bool $value) use ($b) {
			$b->hollow = $value;
		});
		$this->addToggle($this->t(Translation::UI_BRUSH_MENU_DECREMENT), $b->hollow, function(Player $player, bool $value) use ($b) {
			$b->decrementing = $value;
		});
		$this->addSlider($this->t(Translation::UI_BRUSH_MENU_HEIGHT), 0, $loader->config->MaximumSize, 1, $b->height, function(Player $player, float $value) use ($b) {
			$b->height = (int) $value;
		});
		$this->addInput($this->t(Translation::UI_BRUSH_MENU_BLOCKS), $this->processBlocks($b->blocks), "stone,stone_brick:1,2", function(Player $player, string $value) use ($b) {
			$b->blocks = $b->parseBlocks($value);
		});
		$this->addInput($this->t(Translation::UI_BRUSH_MENU_OBSOLETE), $this->processBlocks($b->obsolete), "stone,stone_brick:1,2", function(Player $player, string $value) use ($b) {
			$b->obsolete = $b->parseBlocks($value);
		});
		$this->addInput($this->t(Translation::UI_BRUSH_MENU_BIOME), (string) $b->biomeId, "plains", function(Player $player, string $value) use ($b) {
			$b->biome = $b->parseBiomeId($value);
		});
		$this->addInput($this->t(Translation::UI_BRUSH_MENU_TREE), (string) $b->tree->type, "oak", function(Player $player, string $value) use ($b) {
			$b->tree = $b->parseTree($value);
		});
	}
}