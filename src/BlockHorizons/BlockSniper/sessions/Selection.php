<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\sessions;

use pocketmine\math\AxisAlignedBB;
use pocketmine\math\Vector3;

class Selection{
	/** @var Vector3 */
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
	 * @return AxisAlignedBB
	 */
	public function box() : AxisAlignedBB{
		if(!$this->ready()){
			throw new \InvalidStateException();
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
}