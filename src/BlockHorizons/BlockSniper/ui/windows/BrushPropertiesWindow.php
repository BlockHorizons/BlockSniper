<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\ui\windows;

use BlockHorizons\BlockSniper\brush\Brush;
use BlockHorizons\BlockSniper\brush\types\BiomeType;
use BlockHorizons\BlockSniper\brush\types\ReplaceType;
use BlockHorizons\BlockSniper\data\Translation;
use BlockHorizons\BlockSniper\Loader;
use BlockHorizons\BlockSniper\sessions\SessionManager;
use pocketmine\Player;

class BrushPropertiesWindow extends CustomWindow{

	public function __construct(Loader $loader, Player $requester){
		parent::__construct($this->t(Translation::UI_BRUSH_MENU_TITLE));
		$b = SessionManager::getPlayerSession($requester)->getBrush();

		if($b->mode === Brush::MODE_BRUSH && $b->getType()->usesSize()){
			$this->addSlider($this->t(Translation::UI_BRUSH_MENU_SIZE), 0, $loader->config->maxSize, 1, $b->size, function(Player $player, float $value) use ($b){
				$b->size = (int) $value;
			});
		}
		if($b->mode === Brush::MODE_BRUSH && $b->getType()->usesSize()){
			$this->addToggle($this->t(Translation::UI_BRUSH_MENU_DECREMENT), $b->decrementing, function(Player $player, bool $value) use ($b){
				$b->decrementing = $value;
				// Set the size the brush will reset to after reaching a size of 0.
				$b->resetSize = $b->size;
			});
		}
		if($b->getType()->canBeHollow()){
			$this->addToggle($this->t(Translation::UI_BRUSH_MENU_HOLLOW), $b->hollow, function(Player $player, bool $value) use ($b){
				$b->hollow = $value;
			});
		}
		if($b->mode === Brush::MODE_BRUSH && $b->getShape()->usesHeight()){
			$this->addSlider($this->t(Translation::UI_BRUSH_MENU_HEIGHT), 0, $loader->config->maxSize, 1, $b->height, function(Player $player, float $value) use ($b){
				$b->height = (int) $value;
			});
		}
		if($b->getType()->usesBlocks()){
			$this->addInput($this->t(Translation::UI_BRUSH_MENU_BLOCKS), $b->blocks, "stone,stone_brick:1,2", function(Player $player, string $value) use ($b){
				$b->blocks = $value;
			});
		}

		// Type specific properties.
		switch($b->getType()::ID){
			case ReplaceType::ID:
				$this->addInput($this->t(Translation::UI_BRUSH_MENU_OBSOLETE), $b->obsolete, "stone,stone_brick:1,2", function(Player $player, string $value) use ($b){
					$b->obsolete = $value;
				});
				break;
			case BiomeType::ID:
				$this->addInput($this->t(Translation::UI_BRUSH_MENU_BIOME), (string) $b->biomeId, "plains", function(Player $player, string $value) use ($b){
					$b->biomeId = $b->parseBiomeId($value);
				});
				break;
		}
	}
}