<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockSniper\ui\windows;

use BlockHorizons\BlockSniper\brush\Brush;
use BlockHorizons\BlockSniper\data\Translation;

class GlobalBrushEditMenuWindow extends Window {

	/** @var null|Brush */
	private $brush = null;

	public function process(): void {
		if($this->getBrush() === null) {
			return;
		}
		$shapes = $this->processShapes();
		$types = $this->processTypes();
		$d = $this->getBrush()->jsonSerialize();
		$shapeKey = array_search($d[2], $shapes);
		$typeKey = array_search($d[3], $types);

		$this->data = [
			"type" => "custom_form",
			"title" => Translation::get(Translation::UI_GLOBAL_BRUSH_EDIT_TITLE),
			"content" => [
				[
					"type" => "input",
					"text" => Translation::get(Translation::UI_GLOBAL_BRUSH_EDIT_NAME),
					"default" => $d[0],
					"placeholder" => Translation::get(Translation::UI_GLOBAL_BRUSH_EDIT_NAME)
				],
				[
					"type" => "slider",
					"text" => Translation::get(Translation::UI_GLOBAL_BRUSH_EDIT_SIZE),
					"min" => 0,
					"max" => $this->getLoader()->getConfig()->getMaxRadius(),
					"step" => 1,
					"default" => $d[1]
				],
				[
					"type" => "dropdown",
					"text" => Translation::get(Translation::UI_GLOBAL_BRUSH_EDIT_SHAPE),
					"default" => $shapeKey === false ? 0 : $shapeKey,
					"options" => $shapes
				],
				[
					"type" => "dropdown",
					"text" => Translation::get(Translation::UI_GLOBAL_BRUSH_EDIT_TYPE),
					"default" => $typeKey === false ? 0 : $typeKey,
					"options" => $types
				],
				[
					"type" => "toggle",
					"text" => Translation::get(Translation::UI_GLOBAL_BRUSH_EDIT_HOLLOW),
					"default" => $d[4]
				],
				[
					"type" => "toggle",
					"text" => Translation::get(Translation::UI_GLOBAL_BRUSH_EDIT_DECREMENT),
					"default" => $d[5]
				],
				[
					"type" => "slider",
					"text" => Translation::get(Translation::UI_GLOBAL_BRUSH_EDIT_HEIGHT),
					"min" => 0,
					"max" => $this->getLoader()->getConfig()->getMaxRadius(),
					"step" => 1,
					"default" => $d[6]
				],
				[
					"type" => "toggle",
					"text" => Translation::get(Translation::UI_GLOBAL_BRUSH_EDIT_PERFECT),
					"default" => $d[7]
				],
				[
					"type" => "input",
					"text" => Translation::get(Translation::UI_GLOBAL_BRUSH_EDIT_BLOCKS),
					"placeholder" => "stone,stone_brick:1,2",
					"default" => $d[8]
				],
				[
					"type" => "input",
					"text" => Translation::get(Translation::UI_GLOBAL_BRUSH_EDIT_OBSOLETE),
					"placeholder" => "stone,stone_brick:1,2",
					"default" => $d[9]
				],
				[
					"type" => "input",
					"text" => Translation::get(Translation::UI_GLOBAL_BRUSH_EDIT_BIOME),
					"placeholder" => "plains",
					"default" => $d[10]
				],
				[
					"type" => "input",
					"text" => Translation::get(Translation::UI_GLOBAL_BRUSH_EDIT_TREE),
					"placeholder" => "oak",
					"default" => $d[11]
				]
			]
		];
	}

	/**
	 * @return Brush|null
	 */
	public function getBrush(): ?Brush {
		return $this->brush;
	}

	/**
	 * @param Brush $brush
	 */
	public function setBrush(Brush $brush) {
		$this->brush = $brush;
	}
}