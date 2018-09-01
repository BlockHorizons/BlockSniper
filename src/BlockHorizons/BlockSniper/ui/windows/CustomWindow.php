<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\ui\windows;

use BlockHorizons\BlockSniper\brush\registration\ShapeRegistration;
use BlockHorizons\BlockSniper\brush\registration\TypeRegistration;
use BlockHorizons\BlockSniper\ui\forms\CustomForm;
use pocketmine\Player;

abstract class CustomWindow extends CustomForm{

	public function __construct(string $title){
		parent::__construct($title);
	}

	/**
	 * @param array $blocks
	 *
	 * @return string
	 */
	public function processBlocks(array $blocks) : string{
		$return = [];
		foreach($blocks as $block){
			$return[] = $block->getId() . ":" . $block->getDamage();
		}

		return implode(",", $return);
	}

	/**
	 * @param Player $player
	 *
	 * @return string[]
	 */
	public function processShapes(Player $player) : array{
		$shapes = ShapeRegistration::getShapeIds();
		foreach($shapes as $id => $name){
			if(!$player->hasPermission("blocksniper.shape." . str_replace(" ", "", strtolower($name)))){
				unset($shapes[$id]);
			}
		}

		return array_values($shapes);
	}

	/**
	 * @param Player $player
	 *
	 * @return string[]
	 */
	public function processTypes(Player $player) : array{
		$types = TypeRegistration::getTypeIds();
		foreach($types as $id => $name){
			if(!$player->hasPermission("blocksniper.type." . str_replace(" ", "", strtolower($name)))){
				unset($types[$id]);
			}
		}

		return array_values($types);
	}
}