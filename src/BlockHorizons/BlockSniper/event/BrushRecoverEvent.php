<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\event;

use BlockHorizons\BlockSniper\brush\Brush;
use pocketmine\event\Cancellable;
use pocketmine\event\CancellableTrait;

class BrushRecoverEvent extends BlockSniperEvent implements Cancellable{
	use CancellableTrait;

	/** @var null */
	public static $handlerList = null;

	/** @var string */
	public $player = "";
	/** @var Brush */
	public $brush = null;

	public function __construct(string $player, Brush $brush){
		$this->player = $player;
		$this->brush = $brush;
	}

	/**
	 * Returns the brush (object) that's being recovered.
	 *
	 * @return Brush
	 */
	public function getBrush() : Brush{
		return $this->brush;
	}

	/**
	 * Returns the player name of whom's brush is being recovered.
	 * Warning: The player with this name is highly likely not online!
	 *
	 * @return string
	 */
	public function getPlayer() : string{
		return $this->player;
	}
}