<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockSniper\ui;

use BlockHorizons\BlockSniper\Loader;
use BlockHorizons\BlockSniper\ui\windows\BrushMenuWindow;
use BlockHorizons\BlockSniper\ui\windows\ConfigurationMenuWindow;
use BlockHorizons\BlockSniper\ui\windows\GlobalBrushCreationWindow;
use BlockHorizons\BlockSniper\ui\windows\GlobalBrushDeletionWindow;
use BlockHorizons\BlockSniper\ui\windows\GlobalBrushEditMenuWindow;
use BlockHorizons\BlockSniper\ui\windows\GlobalBrushListWindow;
use BlockHorizons\BlockSniper\ui\windows\GlobalBrushMenuWindow;
use BlockHorizons\BlockSniper\ui\windows\GlobalBrushSelectionMenu;
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
	const WINDOW_GLOBAL_BRUSH_MENU = 4;
	const WINDOW_PRESET_CREATION_MENU = 5;
	const WINDOW_PRESET_DELETION_MENU = 6;
	const WINDOW_PRESET_SELECTION_MENU = 7;
	const WINDOW_PRESET_LIST_MENU = 8;
	const WINDOW_PRESET_EDIT_MENU = 9;
	const WINDOW_GLOBAL_BRUSH_CREATION_MENU = 10;
	const WINDOW_GLOBAL_BRUSH_DELETION_MENU = 11;
	const WINDOW_GLOBAL_BRUSH_SELECTION_MENU = 12;
	const WINDOW_GLOBAL_BRUSH_LIST_MENU = 13;
	const WINDOW_GLOBAL_BRUSH_EDIT_MENU = 14;


	/** @var string[] */
	private $types = [
		MainMenuWindow::class,
		BrushMenuWindow::class,
		PresetMenuWindow::class,
		ConfigurationMenuWindow::class,
		GlobalBrushMenuWindow::class,
		PresetCreationWindow::class,
		PresetDeletionWindow::class,
		PresetSelectionWindow::class,
		PresetListWindow::class,
		PresetEditWindow::class,
		GlobalBrushCreationWindow::class,
		GlobalBrushDeletionWindow::class,
		GlobalBrushSelectionMenu::class,
		GlobalBrushListWindow::class,
		GlobalBrushEditMenuWindow::class
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
			throw new \OutOfBoundsException("Tried to get window of non-existing window ID.");
		}
		return new $this->types[$windowId]($loader, $player);
	}

	/**
	 * @param int $windowId
	 *
	 * @return bool
	 */
	public function isInRange(int $windowId): bool {
		return isset($this->types[$windowId]);
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