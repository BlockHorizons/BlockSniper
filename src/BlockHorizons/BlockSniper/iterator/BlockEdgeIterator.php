<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\iterator;

use pocketmine\block\Block;

class BlockEdgeIterator{

	/** @var Block */
	private $block;

	public function __construct(Block $block){
		$this->block = $block;
	}

	/**
	 * getEdges returns an array with a total of 12 BlockEdges, each describing one of the edges of the block passed.
	 *
	 * @return BlockEdge[]
	 */
	public function getEdges() : array{
		// A block has 12 edges: 4 at the top, 4 on the sides and 4 on the bottom.
		$pos = $this->block->getPosition()->asVector3();

		return [
			// Edges on the bottom of the cube.
			new BlockEdge($pos, $pos->add(0, 0, 1)),
			new BlockEdge($pos, $pos->add(1, 0, 0)),
			new BlockEdge($pos->add(0, 0, 1), $pos->add(1, 0, 1)),
			new BlockEdge($pos->add(1, 0, 0), $pos->add(1, 0, 1)),

			// Edges on the top of the cube.
			new BlockEdge($pos->add(0, 1, 0), $pos->add(0, 1, 1)),
			new BlockEdge($pos->add(0, 1, 0), $pos->add(1, 1, 0)),
			new BlockEdge($pos->add(0, 1, 1), $pos->add(1, 1, 1)),
			new BlockEdge($pos->add(1, 1, 0), $pos->add(1, 1, 1)),

			// Edges on the side of the cube.
			new BlockEdge($pos, $pos->add(0, 1, 0)),
			new BlockEdge($pos->add(0, 0, 1), $pos->add(0, 1, 1)),
			new BlockEdge($pos->add(1, 0, 0), $pos->add(1, 1, 0)),
			new BlockEdge($pos->add(1, 0, 1), $pos->add(1, 1, 1)),
		];
	}
}