<?php

namespace Sandertv\BlockSniper\events;

use pocketmine\event\Cancellable;
use pocketmine\level\Level;
use pocketmine\math\Vector3;

class TypeCreateEvent extends BaseEvent implements Cancellable {
	
	public static $handlerList = null;
	
	public $owner;
	
	/** @noinspection PhpMissingParentConstructorInspection */
	public function __construct($owner) {
		$this->owner = $owner;
	}
	
	/**
	 * Returns the name of the type that's about to be created.
	 *
	 * @return string
	 */
	public function getName(): string {
		return $this->owner->getName();
	}
	
	/**
	 * Returns the permission of the type that's needed to create it.
	 *
	 * @return string
	 */
	public function getPermission(): string {
		return $this->owner->getPermission();
	}
	
	/**
	 * TODO
	 */
	public function getApproximateBlocks(): int {
		// TODO
	}
	
	/**
	 * Returns the radius of the type about to be created.
	 *
	 * @return float
	 */
	public function getRadius(): float {
		return $this->owner->getRadius();
	}
	
	/**
	 * Changes the radius of the type that's about to be created.
	 *
	 * @param float $radius
	 */
	public function setRadius(float $radius) {
		$this->owner->setRadius($radius);
	}
	
	/**
	 * Gets the center of the type on which calculations are based.
	 *
	 * @return Vector3
	 */
	public function getCenter(): Vector3 {
		return $this->owner->getCenter();
	}
	
	/**
	 * Changes the center of the type on which calculations are based.
	 *
	 * @param Vector3 $center
	 */
	public function setCenter(Vector3 $center) {
		$this->owner->setCenter($center);
	}
	
	/**
	 * Returns an array of integers and strings, depending on the blocks given when entering the command.
	 *
	 * @return array
	 */
	public function getBlocks(): array {
		return $this->owner->getBlocks();
	}
	
	/**
	 * Sets an array of blocks, has to be either strings or integers.
	 *
	 * @param array $blocks
	 */
	public function setBlocks(array $blocks) {
		$this->owner->setBlocks($blocks);
	}
	
	/**
	 * Returns the level where the type is about to be created.
	 *
	 * @return Level
	 */
	public function getLevel(): Level {
		return $this->level;
	}
}
