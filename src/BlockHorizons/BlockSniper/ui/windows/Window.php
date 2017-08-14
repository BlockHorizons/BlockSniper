<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockSniper\ui\windows;

use BlockHorizons\BlockSniper\Loader;
use pocketmine\Player;

abstract class Window {

	const ID = -1;
	
	/** @var Loader */
	private $loader = null;
	/** @var Player */
	private $player = null;
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

	protected abstract function process();
}