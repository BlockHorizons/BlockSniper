<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockSniper\ui\windows;

use BlockHorizons\BlockSniper\brush\PropertyProcessor;
use BlockHorizons\BlockSniper\data\Translation;
use BlockHorizons\BlockSniper\sessions\SessionManager;
use pocketmine\level\generator\biome\Biome;
use pocketmine\network\mcpe\protocol\ModalFormResponsePacket;

class BrushMenuWindow extends Window {

	public function process(): void {
		$this->getLoader()->getSessionManager()->createPlayerSession($this->getPlayer());
		$v = SessionManager::getPlayerSession($this->getPlayer())->getBrush();
		$this->data = [
			"type" => "custom_form",
			"title" => (new Translation(Translation::UI_BRUSH_MENU_TITLE))->getMessage(),
			"content" => [
				[
					"type" => "slider",
					"text" => (new Translation(Translation::UI_BRUSH_MENU_SIZE))->getMessage(),
					"min" => 0,
					"max" => $this->getLoader()->getSettings()->getMaxRadius(),
					"step" => 1,
					"default" => $v->getSize()
				],
				[
					"type" => "dropdown",
					"text" => (new Translation(Translation::UI_BRUSH_MENU_SHAPE))->getMessage(),
					"options" => $this->processShapes(),
					"default" => $v->getShape()::ID
				],
				[
					"type" => "dropdown",
					"text" => (new Translation(Translation::UI_BRUSH_MENU_TYPE))->getMessage(),
					"options" => $this->processTypes(),
					"default" => $v->getType()::ID
				],
				[
					"type" => "toggle",
					"text" => (new Translation(Translation::UI_BRUSH_MENU_HOLLOW))->getMessage(),
					"default" => $v->isHollow()
				],
				[
					"type" => "toggle",
					"text" => (new Translation(Translation::UI_BRUSH_MENU_DECREMENT))->getMessage(),
					"default" => $v->isDecrementing()
				],
				[
					"type" => "slider",
					"text" => (new Translation(Translation::UI_BRUSH_MENU_HEIGHT))->getMessage(),
					"min" => 0,
					"max" => $this->getLoader()->getSettings()->getMaxRadius(),
					"default" => $v->getHeight(),
					"step" => 1
				],
				[
					"type" => "toggle",
					"text" => (new Translation(Translation::UI_BRUSH_MENU_PERFECT))->getMessage(),
					"default" => $v->getPerfect()
				],
				[
					"type" => "input",
					"text" => (new Translation(Translation::UI_BRUSH_MENU_BLOCKS))->getMessage(),
					"placeholder" => "stone,stone_brick:1,2",
					"default" => $this->processBlocks($v->getBlocks()),
				],
				[
					"type" => "input",
					"text" => (new Translation(Translation::UI_BRUSH_MENU_OBSOLETE))->getMessage(),
					"placeholder" => "stone,stone_brick:1,2",
					"default" => $this->processBlocks($v->getObsolete()),
				],
				[
					"type" => "input",
					"text" => (new Translation(Translation::UI_BRUSH_MENU_BIOME))->getMessage(),
					"placeholder" => "plains",
					"default" => strtolower(Biome::getBiome($v->getBiomeId())->getName())
				],
				[
					"type" => "input",
					"text" => (new Translation(Translation::UI_BRUSH_MENU_TREE))->getMessage(),
					"placeholder" => "oak",
					"default" => (string) $v->getTreeType()
				]
			]
		];
	}

	public function handle(ModalFormResponsePacket $packet): bool {
		$data = json_decode($packet->formData, true);
		$processor = new PropertyProcessor(SessionManager::getPlayerSession($this->player), $this->loader);
		foreach($data as $key => $value) {
			$processor->process($key, $value);
		}
		return true;
	}
}