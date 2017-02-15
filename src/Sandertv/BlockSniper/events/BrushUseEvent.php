<?php

namespace Sandertv\BlockSniper\events;

use pocketmine\block\Block;
use pocketmine\event\Cancellable;
use pocketmine\level\Level;
use pocketmine\math\Vector3;
use pocketmine\Player;
use Sandertv\BlockSniper\brush\BaseShape;
use Sandertv\BlockSniper\brush\BaseType;
use Sandertv\BlockSniper\brush\Brush;
use Sandertv\BlockSniper\Loader;

class BrushUseEvent extends BaseEvent implements Cancellable {
	
	public static $handlerList = null;
	
	public $owner;
	public $player;
	
	public function __construct(Loader $owner, Player $player) {
		parent::__construct($owner);
		$this->owner = $owner;
		$this->player = $player;
	}
	
	/**
	 * @return Level
	 */
	public function getLevel(): Level {
		return $this->getPlayer()->getLevel();
	}
	
	/**
	 * Returns the player that used the Brush.
	 * @return Player
	 */
	public function getPlayer(): Player {
		return $this->player;
	}
	
	/**
	 * Returns the size of the player that used the Brush.
	 *
	 * @return int
	 */
	public function getSize(): int {
		return Brush::getSize($this->player);
	}
	
	/**
	 * Returns the type of the player that used the Brush. (Object)
	 *
	 * @return BaseType
	 */
	public function getType(): BaseType {
		return Brush::getType($this->player, $this->getShape()->getBlocksInside());
	}
	
	/**
	 * Returns the shape of the player that used the Brush. (Object)
	 *
	 * @return BaseShape
	 */
	public function getShape(): BaseShape {
		return Brush::getShape($this->player);
	}
	
	/**
	 * Returns an array of the blocks that are selected within a shape.
	 *
	 * @return array
	 */
	public function getSelectedBlocks(): array {
		return $this->getShape()->getBlocksInside();
	}
	
	/**
	 * Returns the center of the shape selected.
	 *
	 * @return Vector3
	 */
	public function getCenter(): Vector3 {
		return $this->getShape()->getCenter();
	}
	
	/**
	 * Returns an array of the blocks that will be used to fill the shape.
	 *
	 * @return array
	 */
	public function getBlocks(): array {
		return Brush::getBlocks($this->player);
	}
	
	/**
	 * Returns the height of the brush used, being used by cylinders and cuboids.
	 *
	 * @return int
	 */
	public function getHeight(): int {
		return Brush::getHeight($this->player);
	}
	
	/**
	 * Returns the obsolete block in case of replace type.
	 *
	 * @return Block
	 */
	public function getObsolete(): Block {
		return Brush::getObsolete($this->player);
	}
	
	/**
	 * Returns true if perfect spheres, false if not.
	 * @return bool
	 */
	public function getPerfect(): bool {
		return Brush::getPerfect($this->player);
	}
}
