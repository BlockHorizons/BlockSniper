<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockSniper\ui\windows;

use BlockHorizons\BlockSniper\brush\BaseShape;
use BlockHorizons\BlockSniper\brush\BaseType;
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
	 * @return Player
	 */
	public function getPlayer(): Player {
		return $this->player;
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
	 * @return array
	 */
	public function processShapes(): array {
		$shapes = BaseShape::getShapes();
		foreach($shapes as $key => $shape) {
			if(!$this->getPlayer()->hasPermission("blocksniper.shape." . strtolower(str_replace(" ", "", $shape)))) {
				unset($shapes[$key]);
			}
		}
		return $shapes;
	}

	/**
	 * @return array
	 */
	public function processTypes(): array {
		$types = BaseType::getTypes();
		foreach($types as $key => $type) {
			if(!$this->getPlayer()->hasPermission("blocksniper.type." . strtolower(str_replace(" ", "", $type)))) {
				unset($types[$key]);
			}
		}
		return $types;
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

	protected abstract function process(): void;

	public abstract function handle(ModalFormResponsePacket $packet): bool;
}