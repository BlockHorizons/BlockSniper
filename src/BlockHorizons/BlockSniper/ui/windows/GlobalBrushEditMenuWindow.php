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
			"title" => (new Translation(Translation::UI_GLOBAL_BRUSH_EDIT_TITLE))->getMessage(),
			"content" => [
				[
					"type" => "input",
					"text" => (new Translation(Translation::UI_GLOBAL_BRUSH_EDIT_NAME))->getMessage(),
					"default" => $d[0],
					"placeholder" => (new Translation(Translation::UI_GLOBAL_BRUSH_EDIT_NAME))->getMessage()
				],
				[
					"type" => "slider",
					"text" => (new Translation(Translation::UI_GLOBAL_BRUSH_EDIT_SIZE))->getMessage(),
					"min" => 0,
					"max" => $this->getLoader()->getSettings()->getMaxRadius(),
					"step" => 1,
					"default" => $d[1]
				],
				[
					"type" => "dropdown",
					"text" => (new Translation(Translation::UI_GLOBAL_BRUSH_EDIT_SHAPE))->getMessage(),
					"default" => $shapeKey === false ? 0 : $shapeKey,
					"options" => $shapes
				],
				[
					"type" => "dropdown",
					"text" => (new Translation(Translation::UI_GLOBAL_BRUSH_EDIT_TYPE))->getMessage(),
					"default" => $typeKey === false ? 0 : $typeKey,
					"options" => $types
				],
				[
					"type" => "toggle",
					"text" => (new Translation(Translation::UI_GLOBAL_BRUSH_EDIT_HOLLOW))->getMessage(),
					"default" => $d[4]
				],
				[
					"type" => "toggle",
					"text" => (new Translation(Translation::UI_GLOBAL_BRUSH_EDIT_DECREMENT))->getMessage(),
					"default" => $d[5]
				],
				[
					"type" => "slider",
					"text" => (new Translation(Translation::UI_GLOBAL_BRUSH_EDIT_HEIGHT))->getMessage(),
					"min" => 0,
					"max" => $this->getLoader()->getSettings()->getMaxRadius(),
					"step" => 1,
					"default" => $d[6]
				],
				[
					"type" => "toggle",
					"text" => (new Translation(Translation::UI_GLOBAL_BRUSH_EDIT_PERFECT))->getMessage(),
					"default" => $d[7]
				],
				[
					"type" => "input",
					"text" => (new Translation(Translation::UI_GLOBAL_BRUSH_EDIT_BLOCKS))->getMessage(),
					"placeholder" => "stone,stone_brick:1,2",
					"default" => $d[8]
				],
				[
					"type" => "input",
					"text" => (new Translation(Translation::UI_GLOBAL_BRUSH_EDIT_OBSOLETE))->getMessage(),
					"placeholder" => "stone,stone_brick:1,2",
					"default" => $d[9]
				],
				[
					"type" => "input",
					"text" => (new Translation(Translation::UI_GLOBAL_BRUSH_EDIT_BIOME))->getMessage(),
					"placeholder" => "plains",
					"default" => $d[10]
				],
				[
					"type" => "input",
					"text" => (new Translation(Translation::UI_GLOBAL_BRUSH_EDIT_TREE))->getMessage(),
					"placeholder" => "oak",
					"default" => $d[11]
				]
			]
		];
	}

	/**
	 * @param Brush $brush
	 */
	public function setBrush(Brush $brush) {
		$this->brush = $brush;
	}

	/**
	 * @return Brush|null
	 */
	public function getBrush(): ?Brush {
		return $this->brush;
	}
}