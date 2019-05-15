<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\brush;

use BlockHorizons\BlockSniper\brush\registration\ShapeRegistration;
use pocketmine\math\AxisAlignedBB;
use pocketmine\math\Vector3;
use pocketmine\world\ChunkManager;
use pocketmine\world\World;

/**
 * Class Shape implements the basic behaviour of shapes. It holds a couple of convenience methods which may be used to
 * make processing them easier.
 */
abstract class Shape extends AxisAlignedBB{

	public const ID = -1;

	public const SHAPE_SPHERE = 0;
	public const SHAPE_CUBE = 1;
	public const SHAPE_CUBOID = 2;
	public const SHAPE_CYLINDER = 3;
	public const SHAPE_ELLIPSOID = 4;

	/** @var Vector3 */
	protected $centre;
	/** @var bool */
	protected $hollow = false;

	/**
	 * Shape constructor: Constructs a Shape using the BrushProperties passed to define the bounds of the shape.
	 * The $target passed is assumed to be the target block, which will be used as the centre of the shape if the mode
	 * of the brush is Brush::MODE_BRUSH. If the mode is Brush::MODE_SELECTION, the $selection passed must be non-null
	 * and specify the bounds of the shape.
	 *
	 * @param BrushProperties    $properties
	 * @param Target             $target
	 * @param AxisAlignedBB|null $selection
	 */
	public function __construct(BrushProperties $properties, Target $target, ?AxisAlignedBB $selection = null){
		if($selection === null){
			$selection = new AxisAlignedBB(0, 0, 0, 0, 0, 0);
			$this->buildSelection($target, $properties, $selection);
		}
		parent::__construct($selection->minX, $selection->minY, $selection->minZ, $selection->maxX, $selection->maxY, $selection->maxZ);

		$this->centre = $target->asVector3();
		$this->hollow = $properties->hollow;
	}

	/**
	 * getName returns the name of the shape.
	 *
	 * @return string
	 */
	public abstract function getName() : string;

	/**
	 * getBlocksInside creates a generator that yields all Vector3s that are found within the shape.
	 *
	 * @return \Generator
	 */
	public abstract function getVectors() : \Generator;

	/**
	 * buildSelection builds a selection if the brush mode is Brush::MODE_BRUSH. $center is the target block, and $bb
	 * requires its bounds to be set.
	 *
	 * @param Vector3         $center
	 * @param BrushProperties $properties
	 * @param AxisAlignedBB   $bb
	 */
	public abstract function buildSelection(Vector3 $center, BrushProperties $properties, AxisAlignedBB $bb) : void;

	/**
	 * getBlockCount calculates the total amount of blocks in the selection of the shape.
	 *
	 * @return int
	 */
	public abstract function getBlockCount() : int;

	/**
	 * getPermission returns the permission required to use this shape.
	 *
	 * @return string
	 */
	public function getPermission() : string{
		return "blocksniper.shape." . strtolower(ShapeRegistration::getShapeById(self::ID, true));
	}

	/**
	 * usesThreeLengths defines if the Shape uses three different lengths (width, length, height) to define the
	 * dimensions, instead of a single radius.
	 *
	 * @return bool
	 */
	public function usesThreeLengths() : bool{
		return false;
	}

	/**
	 * getCentre returns the centre of the Shape. This centre might not be accurate, depending on if the Brush mode
	 * was Brush::MODE_BRUSH (accurate) or Brush::MODE_SELECTION (not accurate).
	 *
	 * @return Vector3
	 */
	public function getCentre() : Vector3{
		return $this->centre;
	}

	/**
	 * isHollow defines if the Shape is hollow, as set in the BrushProperties passed into the constructor.
	 *
	 * @return bool
	 */
	public function isHollow() : bool{
		return $this->hollow;
	}

	/**
	 * getBlocks returns a generator that holds blocks rather than Vector3 instances, by looking up the blocks that are
	 * found in the ChunkManager passed.
	 *
	 * @param ChunkManager $manager
	 *
	 * @return \Generator
	 */
	public function getBlocks(ChunkManager $manager) : \Generator{
		foreach($this->getVectors() as $vector){
			yield $manager->getBlockAt($vector->x, $vector->y, $vector->z);
		}
	}

	/**
	 * getTouchedChunks returns a serialised array of chunks that the Shape touches. It uses $chunkManager to find
	 * chunks in.
	 *
	 * @param ChunkManager $chunkManager
	 *
	 * @return string[]
	 */
	public function getTouchedChunks(ChunkManager $chunkManager) : array{
		$touchedChunks = [];
		for($x = $this->minX; $x <= $this->maxX + 16; $x += 16){
			for($z = $this->minZ; $z <= $this->maxZ + 16; $z += 16){
				$chunk = $chunkManager->getChunk($x >> 4, $z >> 4);
				if($chunk === null){
					continue;
				}
				$touchedChunks[World::chunkHash($x >> 4, $z >> 4)] = $chunk->fastSerialize();
			}
		}

		return $touchedChunks;
	}
}
