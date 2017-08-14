<?php

namespace BlockHorizons\BlockSniper\ui\windows;

use BlockHorizons\BlockSniper\brush\BaseShape;
use BlockHorizons\BlockSniper\brush\BaseType;
use BlockHorizons\BlockSniper\brush\BrushManager;
use pocketmine\level\generator\biome\Biome;

class BrushMenuWindow extends Window {

	const ID = 1;

	public function process() {
		$v = BrushManager::get($this->getPlayer());
		$blocks = [];
		foreach($v->getBlocks() as $block) {
			$blocks[] = $block->getId() . ":" . $block->getDamage();
		}
		$blocks = implode(",", $blocks);
		$obsoletes = [];
		foreach($v->getObsolete() as $obsolete) {
			$obsoletes[] = $obsolete->getId() . ":" . $obsolete->getDamage();
		}
		$obsoletes = implode(",", $obsoletes);
		$shapes = BaseShape::getShapes();
		foreach($shapes as $key => $shape) {
			if(!$this->getPlayer()->hasPermission("blocksniper.shape." . strtolower(str_replace(" ", "", $shape)))) {
				unset($shapes[$key]);
			}
		}
		$types = BaseType::getTypes();
		foreach($types as $key => $type) {
			if(!$this->getPlayer()->hasPermission("blocksniper.type." . strtolower(str_replace(" ", "", $type)))) {
				unset($types[$key]);
			}
		}
		$this->data = [
			"type" => "custom_form",
			"title" => "Brush Menu",
			"content" => [
				[
					"type" => "slider",
					"text" => "Brush Size",
					"min" => 0,
					"max" => $this->getLoader()->getSettings()->getMaxRadius(),
					"step" => 1,
					"default" => $v->getSize()
				],
				[
					"type" => "dropdown",
					"text" => "Brush Shape",
					"options" => $shapes,
					"default" => $v->getShape()->getId()
				],
				[
					"type" => "dropdown",
					"text" => "Brush Type",
					"options" => $types,
					"default" => $v->getType()->getId()
				],
				[
					"type" => "toggle",
					"text" => "Hollow Brush",
					"default" => $v->getHollow()
				],
				[
					"type" => "toggle",
					"text" => "Brush Decrement",
					"default" => $v->isDecrementing()
				],
				[
					"type" => "slider",
					"text" => "Brush Height",
					"min" => 0,
					"max" => $this->getLoader()->getSettings()->getMaxRadius(),
					"default" => $v->getHeight(),
					"step" => 1
				],
				[
					"type" => "toggle",
					"text" => "Brush Shape Perfection",
					"default" => $v->getPerfect()
				],
				[
					"type" => "input",
					"text" => "Brush Blocks",
					"placeholder" => "stone,stone_brick:1,2",
					"default" => $blocks
				],
				[
					"type" => "input",
					"text" => "Obsolete Blocks",
					"placeholder" => "stone,stone_brick =>1,2",
					"default" => $obsoletes
				],
				[
					"type" => "input",
					"text" => "Brush Biome",
					"placeholder" => "plains",
					"default" => strtolower(Biome::getBiome($v->getBiomeId())->getName())
				],
				[
					"type" => "input",
					"text" => "Brush Tree",
					"placeholder" => "oak",
					"default" => (string) $v->getTreeType()
				]
			]
		];
	}
}