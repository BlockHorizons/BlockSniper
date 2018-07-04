<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockSniper\ui\windows;

use BlockHorizons\BlockSniper\brush\PropertyProcessor;
use BlockHorizons\BlockSniper\data\Translation;
use BlockHorizons\BlockSniper\sessions\SessionManager;
use pocketmine\level\biome\Biome;
use pocketmine\network\mcpe\protocol\ModalFormResponsePacket;

class BrushMenuWindow extends Window {

	public function process(): void {
		$this->getLoader()->getSessionManager()->createPlayerSession($this->getPlayer());
		$v = SessionManager::getPlayerSession($this->getPlayer())->getBrush();
		$this->data = [
			"type" => "custom_form",
			"title" => Translation::get(Translation::UI_BRUSH_MENU_TITLE),
			"content" => [
				[
					"type" => "slider",
					"text" => Translation::get(Translation::UI_BRUSH_MENU_SIZE),
					"min" => 0,
					"max" => $this->getLoader()->getSettings()->getMaxRadius(),
					"step" => 1,
					"default" => $v->getSize()
				],
				[
					"type" => "dropdown",
					"text" => Translation::get(Translation::UI_BRUSH_MENU_SHAPE),
					"options" => $this->processShapes(),
					"default" => $v->getShape()::ID
				],
				[
					"type" => "dropdown",
					"text" => Translation::get(Translation::UI_BRUSH_MENU_TYPE),
					"options" => $this->processTypes(),
					"default" => $v->getType()::ID
				],
				[
					"type" => "toggle",
					"text" => Translation::get(Translation::UI_BRUSH_MENU_HOLLOW),
					"default" => $v->isHollow()
				],
				[
					"type" => "toggle",
					"text" => Translation::get(Translation::UI_BRUSH_MENU_DECREMENT),
					"default" => $v->isDecrementing()
				],
				[
					"type" => "slider",
					"text" => Translation::get(Translation::UI_BRUSH_MENU_HEIGHT),
					"min" => 0,
					"max" => $this->getLoader()->getSettings()->getMaxRadius(),
					"default" => $v->getHeight(),
					"step" => 1
				],
				[
					"type" => "toggle",
					"text" => Translation::get(Translation::UI_BRUSH_MENU_PERFECT),
					"default" => $v->getPerfect()
				],
				[
					"type" => "input",
					"text" => Translation::get(Translation::UI_BRUSH_MENU_BLOCKS),
					"placeholder" => "stone,stone_brick:1,2",
					"default" => $this->processBlocks($v->getBlocks()),
				],
				[
					"type" => "input",
					"text" => Translation::get(Translation::UI_BRUSH_MENU_OBSOLETE),
					"placeholder" => "stone,stone_brick:1,2",
					"default" => $this->processBlocks($v->getObsolete()),
				],
				[
					"type" => "input",
					"text" => Translation::get(Translation::UI_BRUSH_MENU_BIOME),
					"placeholder" => "plains",
					"default" => strtolower(Biome::getBiome($v->getBiomeId())->getName())
				],
				[
					"type" => "input",
					"text" => Translation::get(Translation::UI_BRUSH_MENU_TREE),
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