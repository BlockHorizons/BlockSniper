<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\ui\window;

use BlockHorizons\BlockSniper\brush\Brush;
use BlockHorizons\BlockSniper\brush\type\BiomeType;
use BlockHorizons\BlockSniper\brush\type\PlantType;
use BlockHorizons\BlockSniper\brush\type\ReplaceType;
use BlockHorizons\BlockSniper\brush\type\TopLayerType;
use BlockHorizons\BlockSniper\brush\type\TreeType;
use BlockHorizons\BlockSniper\data\Translation;
use BlockHorizons\BlockSniper\exception\InvalidItemException;
use BlockHorizons\BlockSniper\Loader;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use pocketmine\world\biome\Biome;
use ReflectionClass;

class BrushPropertiesWindow extends CustomWindow{

	public function __construct(Loader $loader, Brush $b){
		parent::__construct($this->t(Translation::UI_BRUSH_MENU_TITLE));

		if($b->mode === Brush::MODE_BRUSH && $b->getType()->usesSize() && !$b->getShape()->usesThreeLengths()){
			$this->addSlider($this->t(Translation::UI_BRUSH_MENU_SIZE), 0, $loader->config->maxSize, 1, $b->size, function(Player $player, float $value) use ($b){
				$b->size = (int) $value;
			}
			);
		}
		if($b->mode === Brush::MODE_BRUSH && $b->getType()->usesSize() && !$b->getShape()->usesThreeLengths()){
			$this->addToggle($this->t(Translation::UI_BRUSH_MENU_DECREMENT), $b->decrementing, function(Player $player, bool $value) use ($b){
				$b->decrementing = $value;
				// Set the size the brush will reset to after reaching a size of 0.
				$b->resetSize = $b->size;
			}
			);
		}
		if($b->getType()->canBeHollow()){
			$this->addToggle($this->t(Translation::UI_BRUSH_MENU_HOLLOW), $b->hollow, function(Player $player, bool $value) use ($b){
				$b->hollow = $value;
			}
			);
		}
		if($b->mode === Brush::MODE_BRUSH && $b->getShape()->usesThreeLengths() && $b->getType()->usesSize()){
			$this->addSlider($this->t(Translation::UI_BRUSH_MENU_WIDTH), 0, $loader->config->maxSize, 1, $b->width, function(Player $player, float $value) use ($b){
				$b->width = (int) $value;
			}
			);
			if(!($b->getType() instanceof BiomeType)){
				$this->addSlider($this->t(Translation::UI_BRUSH_MENU_HEIGHT), 0, $loader->config->maxSize, 1, $b->height, function(Player $player, float $value) use ($b){
					$b->height = (int) $value;
				}
				);
			}
			$this->addSlider($this->t(Translation::UI_BRUSH_MENU_LENGTH), 0, $loader->config->maxSize, 1, $b->length, function(Player $player, float $value) use ($b){
				$b->length = (int) $value;
			}
			);
		}
		if($b->getType()->usesBrushBlocks()){
			$this->addInput($this->t(Translation::UI_BRUSH_MENU_BLOCKS), $b->brushBlocks, "stone,cracked_stone_brick", function(Player $player, string $value) use ($b){
				try{
					$b->parseBlocks($value);
					$b->brushBlocks = $value;
				}catch(InvalidItemException $exception){
					$player->sendMessage(TextFormat::RED . $exception->getMessage());
				}
			}
			);
		}

		// Type specific properties.
		$type = $b->getType();
		switch(true){
			case $type instanceof TopLayerType:
				$this->addSlider($this->t(Translation::UI_BRUSH_MENU_LAYER_WIDTH), 0, $loader->config->maxSize, 1, $b->layerWidth, function(Player $player, float $value) use ($b){
					$b->layerWidth = (int) $value;
				}
				);
				break;
			case $type instanceof ReplaceType:
				$this->addInput($this->t(Translation::UI_BRUSH_MENU_OBSOLETE), $b->replacedBlocks, "stone,mossy_stone_brick,grass", function(Player $player, string $value) use ($b){
					try{
						$b->parseBlocks($value);
						$b->replacedBlocks = $value;
					}catch(InvalidItemException $exception){
						$player->sendMessage(TextFormat::RED . $exception->getMessage());
					}
				}
				);
				break;
			case $type instanceof BiomeType:
				$this->addInput($this->t(Translation::UI_BRUSH_MENU_BIOME), (string) $b->biomeId, "plains", function(Player $player, string $value) use ($b){
					if(is_numeric($value)){
						$b->biomeId = (int) $value;

						return;
					}
					$biomes = new ReflectionClass(Biome::class);
					$const = strtoupper(str_replace(" ", "_", $value));
					if($biomes->hasConstant($const)){
						$b->biomeId = $biomes->getConstant($const);

						return;
					}
					$player->sendMessage(TextFormat::RED . "Unknown biome type " . $value);
				}
				);
				break;
			case $type instanceof TreeType:
				$this->addTreeProperties($b, $loader);
				break;
			case $type instanceof PlantType:
				$this->addInput($this->t(Translation::UI_BRUSH_MENU_SOIL), $b->soilBlocks, "grass", function(Player $player, string $value) use ($b){
					try{
						$b->parseBlocks($value);
						$b->soilBlocks = $value;
					}catch(InvalidItemException $exception){
						$player->sendMessage(TextFormat::RED . $exception->getMessage());
					}
				}
				);
				break;
		}
	}

	private function addTreeProperties(Brush $b, Loader $loader) : void{
		$this->addInput($this->t(Translation::UI_TREE_MENU_TRUNK_BLOCKS), $b->tree->trunkBlocks, "oak_wood,dark_oak_wood", function(Player $player, string $value) use ($b){
			try{
				$b->parseBlocks($value);
				$b->tree->trunkBlocks = $value;
			}catch(InvalidItemException $exception){
				$player->sendMessage(TextFormat::RED . $exception->getMessage());
			}
		}
		);
		$this->addInput($this->t(Translation::UI_TREE_MENU_LEAVES_BLOCKS), $b->tree->leavesBlocks, "oak_leaves,spruce_leaves", function(Player $player, string $value) use ($b){
			try{
				$b->parseBlocks($value);
				$b->tree->leavesBlocks = $value;
			}catch(InvalidItemException $exception){
				$player->sendMessage(TextFormat::RED . $exception->getMessage());
			}
		}
		);
		$this->addSlider($this->t(Translation::UI_TREE_MENU_TRUNK_HEIGHT), 0, $loader->config->maxSize, 1, $b->tree->trunkHeight, function(Player $player, float $value) use ($b){
			$b->tree->trunkHeight = (int) $value;
		}
		);
		$this->addSlider($this->t(Translation::UI_TREE_MENU_TRUNK_WIDTH), 0, (int) ($loader->config->maxSize / 3), 1, $b->tree->trunkWidth, function(Player $player, float $value) use ($b){
			$b->tree->trunkWidth = (int) $value;
		}
		);
		$this->addSlider($this->t(Translation::UI_TREE_MENU_MAX_BRANCH_LENGTH), 0, $loader->config->maxSize, 1, $b->tree->maxBranchLength, function(Player $player, float $value) use ($b){
			$b->tree->maxBranchLength = (int) $value;
		}
		);
		$this->addSlider($this->t(Translation::UI_TREE_MENU_LEAVES_CLUSTER_SIZE), 0, $loader->config->maxSize / 2, 1, $b->tree->leavesClusterSize, function(Player $player, float $value) use ($b){
			$b->tree->leavesClusterSize = (int) $value;
		}
		);
	}
}