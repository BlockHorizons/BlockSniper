<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\brush;

use BlockHorizons\BlockSniper\brush\async\BlockSniperChunkManager;
use BlockHorizons\BlockSniper\brush\registration\TypeRegistration;
use BlockHorizons\BlockSniper\sessions\SessionManager;
use pocketmine\block\Block;
use pocketmine\level\ChunkManager;
use pocketmine\level\Level;
use pocketmine\level\Position;
use pocketmine\math\Vector2;
use pocketmine\math\Vector3;
use pocketmine\Player;
use pocketmine\Server;
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

	/** @var int */
	protected $level = 0;
	/** @var int */
	protected $biome = 0;
	/** @var \Generator */
	protected $blocks = [];
	/** @var Position|null */
	protected $center = null;
	/** @var Block[]|array */
	protected $obsolete = [];
	/** @var TreeProperties */
	protected $tree = 0;
	/** @var Block[] */
	protected $brushBlocks = [];
	/** @var int */
	protected $height = 0;
	/** @var null|BlockSniperChunkManager */
	protected $chunkManager = null;
	/** @var bool */
	protected $myPlotChecked = false;
	/** @var bool */
	private $async = false;
	/** @var Vector2[][] */
	private $plotPoints = [];

	/**
	 * @param Player       $player
	 * @param ChunkManager $manager
	 * @param \Generator   $blocks
	 */
	public function __construct(Player $player, ChunkManager $manager, \Generator $blocks = null){
		$this->blocks = $blocks;
		$this->brushBlocks = SessionManager::getPlayerSession($player)->getBrush()->getBlocks();

		if($manager instanceof Level){
			$this->level = $manager->getId();
			return;
		}
		$this->async = true;
		$this->chunkManager = $manager;
	}

	/**
	 * @param Vector2[][] $plotPoints
	 *
	 * @return \Generator|null
	 */
	public final function fillShape(array $plotPoints = []) : ?\Generator{
		$this->plotPoints = $plotPoints;
		if(!empty($plotPoints)){
			$this->myPlotChecked = true;
		}
		if(($this->async && $this->canBeExecutedAsynchronously()) || !$this->async){
			return $this->fill();
		}
		return null;
	}

	/**
	 * @return bool
	 */
	public function isAsynchronous() : bool{
		return $this->async;
	}

	/**
	 * @return bool
	 */
	public function canBeExecutedAsynchronously() : bool{
		return true;
	}

	/**
	 * @return \Generator
	 */
	protected abstract function fill() : \Generator;

	/**
	 * @return int
	 */
	public function getLevelId() : int{
		return $this->level;
	}

	/**
	 * @param \Generator|null $blocks
	 *
	 * @return BaseType
	 */
	public function setBlocksInside(?\Generator $blocks) : self{
		$this->blocks = $blocks;

		return $this;
	}

	/**
	 * Returns the blocks the type is being executed upon.
	 *
	 * @return \Generator
	 */
	public function getBlocks() : \Generator{
		return $this->blocks;
	}

	/**
	 * @return array
	 */
	public function getBrushBlocks() : array{
		return $this->brushBlocks;
	}

	/**
	 * Returns the permission required to use the type.
	 *
	 * @return string
	 */
	public function getPermission() : string{
		return "blocksniper.type." . strtolower(TypeRegistration::getTypeById(self::ID, true));
	}

	/**
	 * @return bool
	 */
	public function canBeHollow() : bool{
		return true;
	}

	/**
	 * @return bool
	 */
	public function usesSize() : bool{
		return true;
	}

	/**
	 * @return bool
	 */
	public function usesBlocks() : bool{
		return true;
	}

	/**
	 * @return string
	 */
	public abstract function getName() : string;

	/**
	 * @param bool $value
	 *
	 * @return BaseType
	 */
	public function setAsynchronous(bool $value = true) : self{
		$this->async = $value;

		return $this;
	}

	/**
	 * Puts a block at the given location either asynchronously or synchronously with MyPlot checks. (if relevant)
	 *
	 * @param Vector3 $pos
	 * @param Block $block
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
		if($this->isAsynchronous()){
			$this->getChunkManager()->setBlockAt($pos->x, $pos->y, $pos->z, $block);
			return;
		}
		$this->getLevel()->setBlock($pos, $block, false);
	}

	/**
	 * Deletes a block at the given location either asynchronously or synchronously with MyPlot checks. (if relevant)
	 *
	 * @param Vector3 $pos
	 */
	public function delete(Vector3 $pos) : void{
		$this->putBlock($pos, Block::get(Block::AIR));
	}

	/**
	 * Returns a randomly selected brush block.
	 *
	 * @return Block
	 */
	public function randomBrushBlock() : Block {
		return $this->brushBlocks[array_rand($this->brushBlocks)];
	}

	/**
	 * Returns the side of a block for both asynchronous and synchronous types.
	 *
	 * @param Vector3 $block
	 * @param int     $side
	 *
	 * @return Block
	 */
	public function side(Vector3 $block, int $side) : Block {
		if(!$this->async && $block instanceof Block){
			return $block->getSide($side);
		}
		return $this->chunkManager->getSide($block->x, $block->y, $block->z, $side);
	}

	/**
	 * @return BlockSniperChunkManager
	 */
	public function getChunkManager() : BlockSniperChunkManager{
		return $this->chunkManager;
	}

	/**
	 * @param ChunkManager $manager
	 *
	 * @return BaseType
	 */
	public function setChunkManager(ChunkManager $manager) : self{
		$this->chunkManager = $manager;

		return $this;
	}

	/**
	 * Returns the level the type is used in.
	 *
	 * @return Level|null
	 */
	public function getLevel() : ?Level{
		return Server::getInstance()->getLevel($this->level);
	}

	/**
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
		if($this->isAsynchronous()){
			$this->getChunkManager()->setBiomeIdAt($pos->x, $pos->z, $biomeId);
			return;
		}
		$this->getLevel()->setBiomeId($pos->x, $pos->z, $biomeId);
	}
}
