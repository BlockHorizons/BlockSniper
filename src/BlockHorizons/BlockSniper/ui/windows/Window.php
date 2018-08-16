<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockSniper\ui\windows;

use BlockHorizons\BlockSniper\brush\registration\ShapeRegistration;
use BlockHorizons\BlockSniper\brush\registration\TypeRegistration;
use BlockHorizons\BlockSniper\Loader;
use BlockHorizons\BlockSniper\ui\WindowHandler;
use pocketmine\network\mcpe\protocol\ModalFormRequestPacket;
use pocketmine\network\mcpe\protocol\ModalFormResponsePacket;
use pocketmine\Player;

abstract class Window {

	/** @var Loader */
	protected $loader = null;
	/** @var Player */
	protected $player = null;
	/** @var array */
	protected $data = [];

	public function __construct(Loader $loader, Player $player) {
		$this->loader = $loader;
		$this->player = $player;
		$this->process();
	}

	protected abstract function process(): void;

	/**
	 * @return string
	 */
	public function getJson(): string {
		return json_encode($this->data);
	}

	/**
	 * @return Loader
	 */
	public function getLoader(): Loader {
		return $this->loader;
	}

	/**
	 * @param array $blocks
	 *
	 * @return string
	 */
	public function processBlocks(array $blocks): string {
		$return = [];
		foreach($blocks as $block) {
			$return[] = $block->getId() . ":" . $block->getDamage();
		}
		return implode(",", $return);
	}

	/**
	 * @return string[]
	 */
	public function processShapes(): array {
		$shapes = ShapeRegistration::getShapeIds();
		foreach($shapes as $id => $name) {
			if(!$this->getPlayer()->hasPermission("blocksniper.shape." . str_replace(" ", "", strtolower($name)))) {
				unset($shapes[$id]);
			}
		}
		return array_values($shapes);
	}

	/**
	 * @return Player
	 */
	public function getPlayer(): Player {
		return $this->player;
	}

	/**
	 * @return string[]
	 */
	public function processTypes(): array {
		$types = TypeRegistration::getTypeIds();
		foreach($types as $id => $name) {
			if(!$this->getPlayer()->hasPermission("blocksniper.type." . str_replace(" ", "", strtolower($name)))) {
				unset($types[$id]);
			}
		}
		return array_values($types);
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
		$player->sendDataPacket($packet);
	}

	/**
	 * @param ModalFormResponsePacket $packet
	 *
	 * @return bool
	 */
	public abstract function handle(ModalFormResponsePacket $packet): bool;
}