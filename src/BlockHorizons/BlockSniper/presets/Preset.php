<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\presets;

use BlockHorizons\BlockSniper\brush\BrushProperties;
use BlockHorizons\BlockSniper\sessions\SessionManager;
use pocketmine\Player;

class Preset{

	/** @var string */
	public $name = "";
	/** @var BrushProperties */
	public $properties;

	public function __construct(string $name, BrushProperties $properties = null){
		$this->name = $name;
		if($properties === null){
			$this->properties = new BrushProperties();
		}else{
			$this->properties = $properties;
		}
	}

	/**
	 * Applies the preset on a player.
	 *
	 * @param Player $player
	 */
	public function apply(Player $player) : void{
		$brush = SessionManager::getPlayerSession($player)->getBrush();
		foreach($this->properties as $property => $value){
			$brush->{$property} = $value;
		}
	}
}