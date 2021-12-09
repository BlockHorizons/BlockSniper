<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\session;

use InvalidStateException;
use pocketmine\math\AxisAlignedBB;
use pocketmine\math\Vector3;
use function ceil;
use function max;
use function min;

class Selection{
	/** @var Vector3|null */
	private $pos1, $pos2;

	/**
	 * @param Vector3 $pos
	 */
	public function setFirstPos(Vector3 $pos) : void{
		$this->pos1 = $pos;
	}

	/**
	 * @param Vector3 $pos
	 */
	public function setSecondPos(Vector3 $pos) : void{
		$this->pos2 = $pos;
	}

	/**
	 * @return bool
	 */
	public function ready() : bool{
		return $this->pos1 !== null && $this->pos2 !== null;
	}

	/**
	 * It resets the positions of selection
	 *
	 * @return Selection
	 */
	public function clear() : self{
		$this->pos1 = null;
		$this->pos2 = null;
		return $this;
	}

	/**
	 * @return AxisAlignedBB
	 */
	public function box() : AxisAlignedBB{
		if($this->pos1 === null || $this->pos2 === null){
			throw new \LogicException();
		}

		return new AxisAlignedBB(
			min($this->pos1->x, $this->pos2->x),
			min($this->pos1->y, $this->pos2->y),
			min($this->pos1->z, $this->pos2->z),
			max($this->pos1->x, $this->pos2->x),
			max($this->pos1->y, $this->pos2->y),
			max($this->pos1->z, $this->pos2->z)
		);
	}

	/**
	 * @return int
	 */
	public function blockCount() : int{
		$box = $this->box();

		return (int) ceil(($box->maxX - $box->minX) * ($box->maxY - $box->minY) * ($box->maxZ - $box->minZ));
	}

	/**
	 * getBottomCentre returns the centre block at the bottom of the selection.
	 *
	 * @return Vector3
	 */
	public function getBottomCentre() : Vector3{
		if(!$this->ready()){
			throw new \LogicException("selection must be set before retrieving the centre");
		}
		$bb = $this->box();

		return new Vector3($bb->minX + ($bb->maxX - $bb->minX) / 2, $bb->minY, $bb->minZ + ($bb->maxZ - $bb->minZ) / 2);
	}
}