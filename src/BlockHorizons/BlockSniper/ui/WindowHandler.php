<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockSniper\ui;

use BlockHorizons\BlockSniper\Loader;
use BlockHorizons\BlockSniper\ui\windows\BrushMenuWindow;
use BlockHorizons\BlockSniper\ui\windows\ConfigurationMenuWindow;
use BlockHorizons\BlockSniper\ui\windows\MainMenuWindow;
use BlockHorizons\BlockSniper\ui\windows\PresetCreationWindow;
use BlockHorizons\BlockSniper\ui\windows\PresetDeletionWindow;
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
	const WINDOW_PRESET_SELECTION_MENU = 5;
	const WINDOW_PRESET_DELETION_MENU = 6;
	const WINDOW_PRESET_LIST_MENU = 7;

	/** @var array */
	private $types = [
		MainMenuWindow::class,
		BrushMenuWindow::class,
		PresetMenuWindow::class,
		ConfigurationMenuWindow::class,
		PresetCreationWindow::class,
		PresetSelectionWindow::class,
		PresetDeletionWindow::class,
		//PresetListWindow::class
	];

	/**
	 * @param int    $windowId
	 * @param Loader $loader
	 * @param Player $player
	 *
	 * @return string
	 */
	public function getWindowJson(int $windowId, Loader $loader, Player $player): string {
		if(!isset($this->types[$windowId])) {
			throw new \OutOfBoundsException("Tried to get window json of non-existing window.");
		}
		/** @var Window $window */
		$window = new $this->types[$windowId]($loader, $player);
		return $window->getJson();
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