<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\sessions;

require_once("plugins/BlockSniper/src/marshal/src/Sandertv/Marshal/Unmarshal.php");
require_once("plugins/BlockSniper/src/marshal/src/Sandertv/Marshal/DecodeException.php");

use BlockHorizons\BlockSniper\brush\Brush;
use BlockHorizons\BlockSniper\Loader;
use BlockHorizons\BlockSniper\sessions\owners\PlayerSessionOwner;
use Sandertv\Marshal\DecodeException;
use Sandertv\Marshal\Unmarshal;

class PlayerSession extends Session implements \JsonSerializable{

	/** @var bool */
	private $save = true;

	public function __construct(PlayerSessionOwner $sessionOwner, Loader $loader){
		$this->save = $loader->config->SaveBrushProperties;
		$this->dataFile = $loader->getDataFolder() . "sessions/" . $sessionOwner->getName() . ".json";
		parent::__construct($sessionOwner, $loader);
	}

	/**
	 * @return bool
	 */
	public function initializeBrush() : bool{
		$this->brush = new Brush($this->getSessionOwner()->getPlayerName());

		if($this->save){
			if(!file_exists($this->dataFile)){
				file_put_contents($this->dataFile, "{}");
			}
			$data = file_get_contents($this->dataFile);
			$this->loader->getLogger()->debug("Brush recovered:" . $data);
			try{
				Unmarshal::json($data, $this->brush);
			}catch(DecodeException $exception){
				$this->loader->getLogger()->logException($exception);

				return false;
			}

			return true;
		}

		return false;
	}

	public function __destruct(){
		if($this->save){
			$data = json_encode($this);
			$this->loader->getLogger()->debug("Saved brush:" . $data);
			file_put_contents($this->dataFile, $data);
		}
	}

	/**
	 * @return array
	 */
	public function jsonSerialize() : array{
		return $this->brush->jsonSerialize();
	}
}