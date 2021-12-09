<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\ui\window;

use BlockHorizons\BlockSniper\brush\registration\ShapeRegistration;
use BlockHorizons\BlockSniper\brush\registration\TypeRegistration;
use BlockHorizons\BlockSniper\data\Translation;
use BlockHorizons\BlockSniper\ui\form\CustomForm;
use pocketmine\player\Player;
use function array_values;
use function str_replace;
use function strtolower;
use function ucwords;

abstract class CustomWindow extends CustomForm{

	public function __construct(string $title){
		parent::__construct($title);
	}

	/**
	 * @param Player $player
	 *
	 * @return string[]
	 */
	public function getPermittedShapes(Player $player) : array{
		$shapes = ShapeRegistration::getShapes();
		foreach($shapes as $id => $name){
			if(!$player->hasPermission("blocksniper.shape." . str_replace(" ", "", strtolower($name)))){
				unset($shapes[$id]);
				continue;
			}
			$shapes[$id] = $name;
		}

		return array_values($shapes);
	}

	/**
	 * @param Player $player
	 *
	 * @return string[]
	 */
	public function getPermittedTypes(Player $player) : array{
		$types = TypeRegistration::getTypes();
		foreach($types as $id => $name){
			if(!$player->hasPermission("blocksniper.type." . str_replace(" ", "", strtolower($name)))){
				unset($types[$id]);
				continue;
			}
			$types[$id] = $name;
		}

		return array_values($types);
	}

	/**
	 * @return string[]
	 */
	public function processModes() : array{
		return [
			$this->t(Translation::UI_BRUSH_MENU_MODE_BRUSH),
			$this->t(Translation::UI_BRUSH_MENU_MODE_SELECTION)
		];
	}
}