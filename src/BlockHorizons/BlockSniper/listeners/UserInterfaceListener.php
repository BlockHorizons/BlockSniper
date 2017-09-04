<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockSniper\listeners;

use BlockHorizons\BlockSniper\brush\PropertyProcessor;
use BlockHorizons\BlockSniper\Loader;
use BlockHorizons\BlockSniper\presets\PresetPropertyProcessor;
use BlockHorizons\BlockSniper\sessions\SessionManager;
use BlockHorizons\BlockSniper\ui\WindowHandler;
use BlockHorizons\BlockSniper\ui\windows\PresetEditWindow;
use pocketmine\event\Listener;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\network\mcpe\protocol\ModalFormRequestPacket;
use pocketmine\network\mcpe\protocol\ModalFormResponsePacket;
use pocketmine\Player;

class UserInterfaceListener implements Listener {

	/** @var Loader */
	private $loader = null;

	public function __construct(Loader $loader) {
		$this->loader = $loader;
	}

	/**
	 * @param DataPacketReceiveEvent $event
	 */
	public function onDataPacket(DataPacketReceiveEvent $event): void {
		$packet = $event->getPacket();
		if($packet instanceof ModalFormResponsePacket) {
			if(json_decode($packet->formData, true) === null) {
				return;
			}
			$windowHandler = new WindowHandler();
			switch($packet->formId) {
				case 3200: // Main Menu
					$index = (int) $packet->formData + 1;
					if($index === 4) {
						return;
					}
					$packet = new ModalFormRequestPacket();
					$packet->formId = $windowHandler->getWindowIdFor($index);
					$packet->formData = $windowHandler->getWindowJson($index, $this->loader, $event->getPlayer());
					$event->getPlayer()->dataPacket($packet);
					return;

				case 3201: // Brush Menu
					$data = json_decode($packet->formData, true);
					$processor = new PropertyProcessor(SessionManager::getPlayerSession($event->getPlayer()), $this->loader);
					foreach($data as $key => $value) {
						$processor->process($key, $value);
					}
					$this->navigate(WindowHandler::WINDOW_MAIN_MENU, $event->getPlayer(), $windowHandler);
					return;

				case 3202: // Preset Menu
					$index = (int) $packet->formData + 4;
					$windowHandler = new WindowHandler();
					if($index === 8) {
						$this->navigate(WindowHandler::WINDOW_MAIN_MENU, $event->getPlayer(), $windowHandler);
						return;
					}
					$packet = new ModalFormRequestPacket();
					$packet->formId = $windowHandler->getWindowIdFor($index);
					$packet->formData = $windowHandler->getWindowJson($index, $this->loader, $event->getPlayer());
					$event->getPlayer()->dataPacket($packet);
					return;

				case 3203: // Configuration Menu
					$data = json_decode($packet->formData, true);
					foreach($data as $key => $value) {
						if($key === 1) {
							$value = Loader::getAvailableLanguages()[$value];
						}
						$this->loader->getSettings()->set($key, $value);
					}
					if($data[10] === true) {
						$this->loader->reload();
					}
					$this->navigate(WindowHandler::WINDOW_MAIN_MENU, $event->getPlayer(), $windowHandler);
					return;

				case 3204: // Preset Creation Menu
					$data = json_decode($packet->formData, true);
					if($this->loader->getPresetManager()->isPreset($data[0])) {
						return;
					}
					$processor = new PresetPropertyProcessor($event->getPlayer(), $this->loader);
					foreach($data as $key => $value) {
						$processor->process($key, $value);
					}
					$this->navigate(WindowHandler::WINDOW_PRESET_MENU, $event->getPlayer(), $windowHandler);
					return;

				case 3205: // Preset Deletion Menu
					$index = (int) $packet->formData;
					$presetName = "";
					foreach($this->loader->getPresetManager()->getAllPresets() as $key => $name) {
						if($key === $index) {
							$presetName = $name;
						}
					}
					$this->loader->getPresetManager()->deletePreset($presetName);
					$this->navigate(WindowHandler::WINDOW_PRESET_MENU, $event->getPlayer(), $windowHandler);
					return;

				case 3206: // Preset Selection Menu
					$index = (int) $packet->formData;
					$presetName = "";
					foreach($this->loader->getPresetManager()->getAllPresets() as $key => $name) {
						if($key === $index) {
							$presetName = $name;
						}
					}
					$preset = $this->loader->getPresetManager()->getPreset($presetName);
					$preset->apply($event->getPlayer(), $this->loader);
					$this->navigate(WindowHandler::WINDOW_PRESET_MENU, $event->getPlayer(), $windowHandler);
					return;

				case 3207: // Preset List Menu
					$index = (int) $packet->formData;
					$presetName = "";
					foreach($this->loader->getPresetManager()->getAllPresets() as $key => $name) {
						if($key === $index) {
							$presetName = $name;
						}
					}
					$preset = $this->loader->getPresetManager()->getPreset($presetName);
					/** @var PresetEditWindow $window */
					$window = $windowHandler->getWindow(WindowHandler::WINDOW_PRESET_EDIT_MENU, $this->loader, $event->getPlayer());
					$window->setPreset($preset);
					$window->process();

					$packet = new ModalFormRequestPacket();
					$packet->formId = $windowHandler->getWindowIdFor(WindowHandler::WINDOW_PRESET_EDIT_MENU);
					$packet->formData = $window->getJson();
					$event->getPlayer()->dataPacket($packet);
					return;

				case 3208: // Preset Edit Menu
					$data = json_decode($packet->formData, true);
					$processor = new PresetPropertyProcessor($event->getPlayer(), $this->loader);
					foreach($data as $key => $value) {
						$processor->process($key, $value);
					}
					$this->navigate($windowHandler::WINDOW_PRESET_LIST_MENU, $event->getPlayer(), $windowHandler);
					return;
			}
		}
	}

	/**
	 * @param int           $menu
	 * @param Player        $player
	 * @param WindowHandler $windowHandler
	 */
	public function navigate(int $menu, Player $player, WindowHandler $windowHandler): void {
		$packet = new ModalFormRequestPacket();
		$packet->formId = $windowHandler->getWindowIdFor($menu);
		$packet->formData = $windowHandler->getWindowJson($menu, $this->loader, $player);
		$player->dataPacket($packet);
	}
}