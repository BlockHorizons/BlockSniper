<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockSniper\sessions;

require_once("plugins/BlockSniper/src/marshal/src/Sandertv/Marshal/Unmarshal.php");
require_once("plugins/BlockSniper/src/marshal/src/Sandertv/Marshal/Marshal.php");
require_once("plugins/BlockSniper/src/marshal/src/Sandertv/Marshal/DecodeException.php");

use BlockHorizons\BlockSniper\brush\Brush;
use BlockHorizons\BlockSniper\Loader;
use BlockHorizons\BlockSniper\sessions\owners\PlayerSessionOwner;
use Sandertv\Marshal\DecodeException;
use Sandertv\Marshal\Unmarshal;

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
		$this->brush = new Brush("");
		try {
			Unmarshal::json($data[$this->getSessionOwner()->getPlayerName()]["brush"], $this->brush);
		} catch(DecodeException $exception) {
		}

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
			"brush" => json_encode($this->brush)
		];
	}
}