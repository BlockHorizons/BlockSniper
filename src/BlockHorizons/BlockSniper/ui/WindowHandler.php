<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockSniper\ui;

use BlockHorizons\BlockSniper\Loader;
use BlockHorizons\BlockSniper\ui\windows\BrushMenuWindow;
use BlockHorizons\BlockSniper\ui\windows\ConfigurationMenuWindow;
use BlockHorizons\BlockSniper\ui\windows\MainMenuWindow;
use BlockHorizons\BlockSniper\ui\windows\PresetCreationWindow;
use BlockHorizons\BlockSniper\ui\windows\PresetDeletionWindow;
use BlockHorizons\BlockSniper\ui\windows\PresetEditWindow;
use BlockHorizons\BlockSniper\ui\windows\PresetListWindow;
use BlockHorizons\BlockSniper\ui\windows\PresetMenuWindow;
use BlockHorizons\BlockSniper\ui\windows\PresetSelectionWindow;
use BlockHorizons\BlockSniper\ui\windows\Window;
use pocketmine\Player;

class WindowHandler {

	const WINDOW_MAIN_MENU = 0;
	const WINDOW_BRUSH_MENU = 1;
	const WINDOW_PRESET_MENU = 2;
	const WINDOW_CONFIGURATION_MENU = 3;
	const WINDOW_PRESET_CREATION_MENU = 4;
	const WINDOW_PRESET_DELETION_MENU = 5;
	const WINDOW_PRESET_SELECTION_MENU = 6;
	const WINDOW_PRESET_LIST_MENU = 7;
	const WINDOW_PRESET_EDIT_MENU = 8;

	/** @var array */
	private $types = [
		MainMenuWindow::class,
		BrushMenuWindow::class,
		PresetMenuWindow::class,
		ConfigurationMenuWindow::class,
		PresetCreationWindow::class,
		PresetDeletionWindow::class,
		PresetSelectionWindow::class,
		PresetListWindow::class,
		PresetEditWindow::class
	];

	/**
	 * @param int    $windowId
	 * @param Loader $loader
	 * @param Player $player
	 *
	 * @return string
	 */
	public function getWindowJson(int $windowId, Loader $loader, Player $player): string {
		return $this->getWindow($windowId, $loader, $player)->getJson();
	}

	/**
	 * @param int    $windowId
	 * @param Loader $loader
	 * @param Player $player
	 *
	 * @return Window
	 */
	public function getWindow(int $windowId, Loader $loader, Player $player): Window {
		if(!isset($this->types[$windowId])) {
			throw new \OutOfBoundsException("Tried to get window json of non-existing window.");
		}
		return new $this->types[$windowId]($loader, $player);
	}

	/**
	 * @param int $windowId
	 *
	 * @return int
	 */
	public function getWindowIdFor(int $windowId): int {
		return 3200 + $windowId;
	}
}