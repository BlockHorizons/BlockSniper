<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\revert;

use pocketmine\world\World;

abstract class Revert{

	/** @var World */
	private $world;

	public function __construct(World $world){
		$this->world = $world;
	}

	/**
	 * @return World
	 */
	public function getWorld() : World{
		return $this->world;
	}

	/**
	 * @return Revert
	 */
	public abstract function restore() : Revert;
}