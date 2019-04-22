<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\brush;

use pocketmine\level\ChunkManager;
use pocketmine\level\Position;
use pocketmine\math\Vector3;

/**
 * Class Target is a wrapper around a Vector3, providing roughly the same functionality as a pocketmine\level\Position,
 * but allowing a generic ChunkManager instead of a Level instance.
 *
 * @package BlockHorizons\BlockSniper\brush
 */
class Target extends Vector3{

	/** @var ChunkManager|null */
	private $chunkManager;

	public function __construct(Vector3 $position, ?ChunkManager $chunkManager){
		parent::__construct($position->x, $position->y, $position->z);
		$this->chunkManager = $chunkManager;
	}

	/**
	 * getChunkManager returns the ChunkManager of the Target, or null if null was passed when constructing the Target.
	 *
	 * @return ChunkManager|null
	 */
	public function getChunkManager() : ?ChunkManager{
		return $this->chunkManager;
	}

	/**
	 * fromPosition creates a Target instance from a Position instance.
	 *
	 * @param Position $position
	 *
	 * @return Target
	 */
	public static function fromPosition(Position $position) : Target {
		return new Target($position->asVector3(), $position->getLevel());
	}
}