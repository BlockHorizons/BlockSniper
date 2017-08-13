<?php

namespace BlockHorizons\BlockSniper\listeners;

use BlockHorizons\BlockSniper\brush\PropertyProcessor;
use BlockHorizons\BlockSniper\Loader;
use BlockHorizons\BlockSniper\user_interface\WindowHandler;
use pocketmine\event\Listener;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\network\mcpe\protocol\ModalFormRequestPacket;
use pocketmine\network\mcpe\protocol\ModalFormResponsePacket;

class UserInterfaceListener implements Listener {

	/** @var Loader */
	private $loader = null;

	public function __construct(Loader $loader) {
		$this->loader = $loader;
	}

	/**
	 * @param DataPacketReceiveEvent $event
	 */
	public function onDataPacket(DataPacketReceiveEvent $event) {
		$packet = $event->getPacket();
		if($packet instanceof ModalFormResponsePacket) {
			if(empty($packet->formData) || $packet->formData === ".") {
				return;
			}
			switch($packet->formId) {
				case 3200: // Main Menu
					$index = (int) $packet->formData + 1;
					$windowHandler = new WindowHandler();
					switch($index) {
						case 1:
							$json = $windowHandler->getBrushWindowJson($event->getPlayer(), $this->loader);
							break;
						case 3:
							$json = $windowHandler->getConfigurationWindowJson($this->loader);
							break;
						default:
							$json = $windowHandler->getWindowJson($index);
							break;
					}
					if($index !== 4) {
						$packet = new ModalFormRequestPacket();
						$packet->formId = $windowHandler->getWindowIdFor($index);
						$packet->formData = $json;
						$event->getPlayer()->dataPacket($packet);
					}
					return;

				case 3201: // Brush Menu
					$data = (array) json_decode($packet->formData);
					foreach($data as $key => $value) {
						(new PropertyProcessor($key, $value, $event->getPlayer(), $this->loader))->process();
					}
					break;

				case 3202: // Preset Menu
					$index = (int) $packet->formData + 4;
					$windowHandler = new WindowHandler();
					switch($index) {
						case 4:
							$data = (array) json_decode($windowHandler->getBrushWindowJson($event->getPlayer(), $this->loader), true);
							$data["title"] = "Preset Creation Menu";
							$json = json_encode($data);
							break;
						case 5:
							$json = '';
							break;
						default:
							$json = $windowHandler->getWindowJson($index);
					}
					if($index !== 8) {
						$packet = new ModalFormRequestPacket();
						$packet->formId = $windowHandler->getWindowIdFor($index);
						$packet->formData = $json;
						$event->getPlayer()->dataPacket($packet);
					}
					break;

				case 3203: // Configuration Menu
					$data = (array) json_decode($packet->formData);
					break;

				case 3204: // Preset Creation Menu
					$data = (array) json_decode($packet->formData);
			}
		}
	}
}