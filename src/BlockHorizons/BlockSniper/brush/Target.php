<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\brush;

use pocketmine\level\ChunkManager;
use pocketmine\math\Vector3;

class Target extends Vector3{

	/** @var ChunkManager|null */
	private $chunkManager;

	public function __construct(Vector3 $position, ?ChunkManager $chunkManager){
		parent::__construct($position->x, $position->y, $position->z);
		$this->chunkManager = $chunkManager;
	}

	/**
	 * @return ChunkManager|null
	 */
	public function getChunkManager() : ?ChunkManager{
		return $this->chunkManager;
	}
}