<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\brush;

use BlockHorizons\BlockSniper\brush\async\BlockSniperChunkManager;
use BlockHorizons\BlockSniper\brush\registration\TypeRegistration;
use BlockHorizons\BlockSniper\exceptions\InvalidItemException;
use pocketmine\block\Block;
use pocketmine\level\ChunkManager;
use pocketmine\level\Level;
use pocketmine\math\Vector2;
use pocketmine\math\Vector3;
use function array_rand;
use function strtolower;

abstract class BaseType{

	public const ID = -1;

	public const TYPE_BIOME = 0;
	public const TYPE_CLEAN_ENTITIES = 1;
	public const TYPE_CLEAN = 2;
	public const TYPE_DRAIN = 3;
	public const TYPE_EXPAND = 4;
	public const TYPE_FILL = 5;
	public const TYPE_FLATTEN_ALL = 6;
	public const TYPE_FLATTEN = 7;
	public const TYPE_LAYER = 8;
	public const TYPE_LEAF_BLOWER = 9;
	public const TYPE_MELT = 10;
	public const TYPE_OVERLAY = 11;
	public const TYPE_REPLACE_ALL = 12;
	public const TYPE_REPLACE = 13;
	public const TYPE_SNOW_CONE = 14;
	public const TYPE_TOP_LAYER = 15;
	public const TYPE_TREE = 16;
	public const TYPE_REGENERATE = 17;
	public const TYPE_FREEZE = 18;
	public const TYPE_WARM = 19;
	public const TYPE_HEAT = 20;
	public const TYPE_SMOOTH = 21;
	public const TYPE_REPLACE_TARGET = 22;
	public const TYPE_PLANT = 23;

	/** @var \Generator */
	protected $blocks = [];
	/** @var Block[] */
	protected $brushBlocks = [];
	/** @var Vector3 */
	protected $target;

	/** @var BlockSniperChunkManager|Level */
	protected $chunkManager = null;
	/** @var bool */
	protected $myPlotChecked = false;
	/** @var Vector2[][] */
	protected $plotPoints = [];

	/**
	 * BaseType constructor: Constructs a new BaseType using the BrushProperties passed. The Type was executed while
	 * having $target as target block. The $blocks passed may be null, but must later be supplied using setBlocksInside
	 * in order to operate.
	 *
	 * @param BrushProperties $properties
	 * @param Target          $target
	 * @param \Generator|null $blocks
	 */
	public function __construct(BrushProperties $properties, Target $target, \Generator $blocks = null){
		$this->blocks = $blocks;
		$this->chunkManager = $target->getChunkManager();
		$this->target = $target->asVector3();
		try{
			$this->brushBlocks = $properties->getBlocks();
		}catch(InvalidItemException $exception){
			$this->brushBlocks = [Block::get(Block::AIR)];
		}
	}

	/**
	 * getTarget returns the target block of the BaseType.
	 *
	 * @return Vector3
	 */
	protected function getTarget() : Vector3{
		return $this->target;
	}

	/**
	 * canBeHollow defines if the Type uses the hollow property. Types such as the Regenerate Type do not use this, as
	 * they operate only on the target block and have no realisation of hollow.
	 *
	 * @return bool
	 */
	public function canBeHollow() : bool{
		return true;
	}

	/**
	 * usesSize defines if the Type uses the size property. Types such as the Regenerate Type do not use this, as they
	 * operate only on the target block.
	 *
	 * @return bool
	 */
	public function usesSize() : bool{
		return true;
	}

	/**
	 * usesBrushBlocks defines if the Type uses brush blocks. Types such as the Regenerate Type do not use this, and
	 * overwrite the method.
	 *
	 * @return bool
	 */
	public function usesBrushBlocks() : bool{
		return true;
	}

	/**
	 * canBeExecutedAsynchronously defines if the Type is able to be executed asynchronously. If not, the Type will
	 * always be executed synchronously, no matter the size of the shape.
	 *
	 * @return bool
	 */
	public function canBeExecutedAsynchronously() : bool{
		return true;
	}

	/**
	 * getPermission returns the permission required to use the Type.
	 *
	 * @return string
	 */
	public function getPermission() : string{
		return "blocksniper.type." . strtolower(TypeRegistration::getTypeById(self::ID, true));
	}

	/**
	 * getName returns the name of the Type.
	 *
	 * @return string
	 */
	public abstract function getName() : string;

	/**
	 * fill fills the blocks set in the generator of the BaseType, and in turn returns a generator yielding the undo
	 * blocks of the Type.
	 *
	 * @return \Generator
	 */
	protected abstract function fill() : \Generator;

	/**
	 * fillShape fills the shape previously set in the constructor or using setBlocksInside() and edits the blocks
	 * supplied the way the Type defines. If $plotPoints is not empty, modifications will only be allowed to happen
	 * within these points.
	 *
	 * @param Vector2[][] $plotPoints
	 *
	 * @return \Generator|null
	 */
	public final function fillShape(array $plotPoints = []) : ?\Generator{
		$this->plotPoints = $plotPoints;
		if(!empty($plotPoints)){
			$this->myPlotChecked = true;
		}
		$isLevel = $this->chunkManager instanceof Level;
		if((!$isLevel && $this->canBeExecutedAsynchronously()) || $isLevel){
			return $this->fill();
		}

		return null;
	}

	/**
	 * randomBrushBlock returns a random brush block set in the BrushProperties passed during construction of the
	 * BaseType.
	 *
	 * @return Block
	 */
	public function randomBrushBlock() : Block{
		return $this->brushBlocks[array_rand($this->brushBlocks)];
	}

	/**
	 * getBlock returns the block at a specific position, provided the chunk of that block is loaded into the
	 * ChunkManager.
	 *
	 * @param Vector3 $pos
	 *
	 * @return Block
	 */
	public function getBlock(Vector3 $pos) : Block{
		return $this->chunkManager->getBlockAt($pos->x, $pos->y, $pos->z);
	}

	/**
	 * side returns the neighbouring block of the block passed at the side passed, provided the ChunkManager of the
	 * BaseType is not null.
	 *
	 * @param Vector3 $block
	 * @param int     $side
	 *
	 * @return Block
	 */
	public function side(Vector3 $block, int $side) : Block{
		if($this->chunkManager instanceof Level && $block instanceof Block){
			return $block->getSide($side);
		}

		return $this->chunkManager->getSide($block->x, $block->y, $block->z, $side);
	}

	/**
	 * putBiome puts a biome ID at a specific position, provided it is within the plot areas defined when constructing
	 * the type (if any at all).
	 *
	 * @param Vector3 $pos
	 * @param int     $biomeId
	 */
	protected function putBiome(Vector3 $pos, int $biomeId) : void{
		$valid = !$this->myPlotChecked;
		if($this->myPlotChecked){
			foreach($this->plotPoints as $plotCorners){
				if($pos->x < $plotCorners[0]->x || $pos->z < $plotCorners[0]->z || $pos->x > $plotCorners[1]->x || $pos->z > $plotCorners[1]->z){
					continue;
				}
				$valid = true;
				break;
			}
		}
		if(!$valid){
			return;
		}
		$this->chunkManager->setBiomeId($pos->x, $pos->z, $biomeId);
	}

	/**
	 * putBlock puts a block at the given location in the ChunkManager of the BaseType, provided it is not null. The
	 * block is only set if it is within the plot boundaries set during construction of the BaseType.
	 *
	 * @param Vector3 $pos
	 * @param Block   $block
	 */
	public function putBlock(Vector3 $pos, Block $block) : void{
		$valid = !$this->myPlotChecked;
		if($pos->y < 0 || $pos->y >= Level::Y_MAX){
			return;
		}
		if($this->myPlotChecked){
			foreach($this->plotPoints as $plotCorners){
				if($pos->x < $plotCorners[0]->x || $pos->z < $plotCorners[0]->y || $pos->x > $plotCorners[1]->x || $pos->z > $plotCorners[1]->y){
					continue;
				}
				$valid = true;
				break;
			}
		}
		if(!$valid){
			return;
		}
		$this->chunkManager->setBlockAt($pos->x, $pos->y, $pos->z, $block, false);
	}

	/**
	 * delete removes a block at the given location provided the ChunkManager of the BaseType is not null. This
	 * method is a wrapper around putBlock(air).
	 *
	 * @param Vector3 $pos
	 */
	public function delete(Vector3 $pos) : void{
		$this->putBlock($pos, Block::get(Block::AIR));
	}

	/**
	 * getChunkManager returns the ChunkManager of the BaseType. If not set, or if passed onto an AsyncTask before, the
	 * ChunkManager returned is null.
	 *
	 * @return BlockSniperChunkManager|Level
	 */
	public function getChunkManager() : ?ChunkManager{
		return $this->chunkManager;
	}

	/**
	 * setChunkManager sets the ChunkManager of the BaseType, and returns itself for fluency. A BaseType loses its
	 * ChunkManager when passed onto an AsyncTask, so it must be reset.
	 *
	 * @param ChunkManager $manager
	 *
	 * @return BaseType
	 */
	public function setChunkManager(ChunkManager $manager) : self{
		$this->chunkManager = $manager;

		return $this;
	}

	/**
	 * setBlocksInside sets the generator used to supply blocks to the Type to edit.
	 *
	 * @param \Generator|null $blocks
	 *
	 * @return BaseType
	 */
	public function setBlocksInside(?\Generator $blocks) : self{
		$this->blocks = $blocks;

		return $this;
	}

	/**
	 * __sleep returns only specific properties of BaseType that can be serialised when passed onto an AsyncTask.
	 *
	 * @return string[]
	 */
	public function __sleep(){
		return ["brushBlocks", "target", "myPlotChecked", "plotPoints"];
	}
}
