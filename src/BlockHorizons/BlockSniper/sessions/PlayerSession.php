<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\sessions;

use BlockHorizons\BlockSniper\brush\Brush;
use BlockHorizons\BlockSniper\Loader;
use BlockHorizons\BlockSniper\sessions\owners\PlayerSessionOwner;
use Sandertv\Marshal\DecodeException;
use Sandertv\Marshal\Unmarshal;

class PlayerSession extends Session implements \JsonSerializable{

	public function __construct(PlayerSessionOwner $sessionOwner, Loader $loader){
		$this->dataFile = $loader->getDataFolder() . "sessions/" . strtolower($sessionOwner->getName()) . ".json";
		parent::__construct($sessionOwner, $loader);
	}

	/**
	 * @return bool true if the brush could be recovered from a file.
	 */
	public function initializeBrush() : bool{
		$this->brush = new Brush($this->getSessionOwner()->getPlayerName());

		if($this->loader->config->saveBrushProperties){
			if(!file_exists($this->dataFile)){
				file_put_contents($this->dataFile, "{}");
			}else{
				$data = file_get_contents($this->dataFile);
				try{
					Unmarshal::json($data, $this->brush);
					$this->loader->getLogger()->debug("Brush recovered:" . $data);
				}catch(DecodeException $exception){
					$this->loader->getLogger()->logException($exception);

					return false;
				}
			}

			return true;
		}

		return false;
	}

	public function close() : void{
		if($this->loader->config->saveBrushProperties){
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