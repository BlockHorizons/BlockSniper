<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\iterator;

use Generator;
use pocketmine\math\Vector3;

class BlockEdge{

	/** @var Vector3 */
	private $start, $end;

	public function __construct(Vector3 $start, Vector3 $end){
		$this->start = $start;
		$this->end = $end;
	}

	/**
	 * walk walks from the start of the edge to the end, taking steps of $interval size. The generator yields Vector3s.
	 *
	 * @param float $interval
	 *
	 * @return Generator|Vector3[]
	 */
	public function walk(float $interval = 0.1) : Generator{
		$sub = $this->end->subtractVector($this->start)->multiply($interval);
		$iterCount = 1 / $interval;
		for($i = 0; $i < $iterCount + 0.0001; $i++){
			yield $this->start->addVector($sub->multiply($i));
		}
	}

	/**
	 * getStart returns the start position of the block edge: One of the corners of the block.
	 *
	 * @return Vector3
	 */
	public function getStart() : Vector3{
		return $this->start;
	}

	/**
	 * getEnd returns the end position of the block edge: One of the corners of the block.
	 *
	 * @return Vector3
	 */
	public function getEnd() : Vector3{
		return $this->end;
	}
}