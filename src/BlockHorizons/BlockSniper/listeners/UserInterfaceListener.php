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
			$packet->formId = $windowHandler->getWindowIdFor($packet->formId);
			if(!$windowHandler->isInRange($packet->formId)) {
				return;
			}
			$window = $windowHandler->getWindow($packet->formId, $this->loader, $event->getPlayer());
			$window->handle($packet);
		}
	}
}