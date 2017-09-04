<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockSniper\sessions;

use BlockHorizons\BlockSniper\brush\Brush;
use BlockHorizons\BlockSniper\Loader;
use BlockHorizons\BlockSniper\sessions\owners\ISessionOwner;
use pocketmine\level\Position;

class ServerSession extends Session implements \JsonSerializable {

	/** @var Position */
	private $targetBlock = null;
	/** @var string */
	private $levelName = "";

	public function __construct(ISessionOwner $sessionOwner, Loader $loader) {
		$this->dataFile = $loader->getDataFolder() . "serverSessions.json";
		parent::__construct($sessionOwner, $loader);
	}

	/**
	 * @param Position $position
	 */
	public function setTargetBlock(Position $position) {
		$this->levelName = $position->getLevel()->getName();
		$this->targetBlock = $position;
	}

	/**
	 * @return Position
	 */
	public function getTargetBlock(): Position {
		return $this->targetBlock;
	}

	/**
	 * @return bool
	 */
	protected function initializeBrush(): bool {
		$this->brush = new Brush($this->getSessionOwner()->getName());
		return true;
	}

	/**
	 * @return array
	 */
	public function jsonSerialize(): array {
		return [
			"targetBlock" => [
				"level" => $this->levelName,
				"x" => $this->targetBlock->x,
				"y" => $this->targetBlock->y,
				"z" => $this->targetBlock->z
			],
			"brush" => $this->getBrush()->jsonSerialize()
		];
	}

	public function __destruct() {
		$data = json_decode(file_get_contents($this->getDataFile()), true);
		$data[] = $this->jsonSerialize();
		file_put_contents($this->getDataFile(), json_encode($data));
	}
}