<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\session;

use BlockHorizons\BlockSniper\brush\Brush;
use BlockHorizons\BlockSniper\Loader;
use BlockHorizons\BlockSniper\session\owner\ServerSessionOwner;
use JsonSerializable;
use pocketmine\world\Position;
use function file_get_contents;
use function file_put_contents;
use function json_decode;
use function json_encode;

/**
 * @phpstan-extends Session<ServerSessionOwner>
 */
class ServerSession extends Session implements JsonSerializable{

	/** @var Position */
	private $targetBlock = null;
	/** @var string */
	private $worldName = "";
	/** @var string */
	private $name = "";

	public function __construct(ServerSessionOwner $sessionOwner, Loader $loader){
		$this->dataFile = $loader->getDataFolder() . "serverSessions.json";
		parent::__construct($sessionOwner, $loader);
	}

	/**
	 * @return string
	 */
	public function getName() : string{
		return $this->name;
	}

	/**
	 * @param string $name
	 */
	public function setName(string $name) : void{
		$this->name = $name;
	}

	/**
	 * @return Position
	 */
	public function getTargetBlock() : Position{
		return $this->targetBlock;
	}

	/**
	 * @param Position $position
	 */
	public function setTargetBlock(Position $position) : void{
		$this->worldName = $position->getWorld()->getFolderName();
		$this->targetBlock = $position;
	}

	public function __destruct(){
		$data = json_decode(file_get_contents($this->getDataFile()), true);
		$data[] = $this->jsonSerialize();
		file_put_contents($this->getDataFile(), json_encode($data));
	}

	/**
	 * @return mixed[]
	 */
	public function jsonSerialize() : array{
		return [
			"targetBlock" => [
				"world" => $this->worldName,
				"x" => $this->targetBlock->x,
				"y" => $this->targetBlock->y,
				"z" => $this->targetBlock->z
			],
			"brush" => $this->getBrush()->jsonSerialize(),
			"name" => $this->name
		];
	}

	/**
	 * @return bool
	 */
	protected function initializeBrush() : bool{
		$this->brush = new Brush();

		return true;
	}
}