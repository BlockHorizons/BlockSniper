<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockSniper\sessions;

use BlockHorizons\BlockSniper\brush\Brush;
use BlockHorizons\BlockSniper\Loader;
use BlockHorizons\BlockSniper\sessions\owners\PlayerSessionOwner;

class PlayerSession extends Session implements \JsonSerializable {

	public function __construct(PlayerSessionOwner $sessionOwner, Loader $loader) {
		$this->dataFile = $loader->getDataFolder() . "sessions/players.json";
		parent::__construct($sessionOwner, $loader);
	}

	/**
	 * @return bool
	 */
	public function initializeBrush(): bool {
		$data = json_decode(file_get_contents($this->getDataFile()), true);
		if(!isset($data[$this->getSessionOwner()->getPlayerName()])) {
			$this->brush = new Brush($this->getSessionOwner()->getPlayerName());
			return false;
		}
		$this->brush = unserialize($data[$this->getSessionOwner()->getPlayerName()]["brush"]);
		return true;
	}

	public function __destruct() {
		$data = json_decode(file_get_contents($this->getDataFile()), true);
		$data[$this->getSessionOwner()->getPlayerName()] = $this->jsonSerialize();
		file_put_contents($this->getDataFile(), json_encode($data));
	}

	/**
	 * @return array
	 */
	public function jsonSerialize(): array {
		return [
			"brush" => serialize($this->brush)
		];
	}
}