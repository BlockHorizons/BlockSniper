<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\events;

use pocketmine\event\Cancellable;
use pocketmine\Player;

class PresetCreationEvent extends BlockSniperEvent implements Cancellable{

	/** @var null */
	public static $handlerList = null;

	/** @var Player */
	private $player = null;
	/** @var array */
	private $presetData = [];

	public function __construct(Player $player, array $presetData){
		$this->player = $player;
		$this->presetData = $presetData;
	}

	/**
	 * Returns the player that created the preset.
	 *
	 * @return Player
	 */
	public function getPlayer() : Player{
		return $this->player;
	}

	/**
	 * Returns an array looking like this:
	 *  "name" => string,
	 *  "type" => string,
	 *  "decrement" => bool,
	 *  "perfect" => bool,
	 *  "size" => int,
	 *  "hollow" => bool,
	 *  "blocks" => array,
	 *  "obsolete" => array,
	 *  "height" => int,
	 *  "biome" => string|int
	 *
	 * @return array
	 */
	public function getPresetData() : array{
		return $this->presetData;
	}
}