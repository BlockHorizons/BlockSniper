<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockSniper\ui\windows;

use BlockHorizons\BlockSniper\brush\BrushManager;
use pocketmine\level\generator\biome\Biome;

class BrushMenuWindow extends Window {

	const ID = 1;

	public function process() {
		$v = BrushManager::get($this->getPlayer());
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
					"options" => $this->processShapes(),
					"default" => $v->getShape()->getId()
				],
				[
					"type" => "dropdown",
					"text" => "Brush Type",
					"options" => $this->processTypes(),
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
					"default" => $this->processBlocks($v->getBlocks()),
				],
				[
					"type" => "input",
					"text" => "Obsolete Blocks",
					"placeholder" => "stone,stone_brick =>1,2",
					"default" => $this->processBlocks($v->getObsolete()),
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