<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\brush;

use BlockHorizons\BlockSniper\brush\async\tasks\BrushTask;
use BlockHorizons\BlockSniper\brush\registration\ShapeRegistration;
use pocketmine\level\Level;
use pocketmine\level\Position;
use pocketmine\math\AxisAlignedBB;
use pocketmine\math\Vector2;
use pocketmine\math\Vector3;
use pocketmine\Player;
use pocketmine\Server;

abstract class BaseShape extends AxisAlignedBB{

	const ID = -1;

	const SHAPE_SPHERE = 0;
	const SHAPE_CUBE = 1;
	const SHAPE_CUBOID = 2;
	const SHAPE_CYLINDER = 3;

	/** @var int */
	protected $level = 0;
	/** @var Vector3 */
	protected $center;
	/** @var bool */
	protected $hollow = false;
	/** @var string */
	protected $playerName = "";

	public function __construct(Player $player, Level $level, Position $center, ?AxisAlignedBB $bb, Brush $brush){
		if($bb === null){
			$bb = new AxisAlignedBB(0, 0, 0, 0, 0, 0);
			$this->buildSelection($center, $brush, $bb);
		}
		parent::__construct($bb->minX, $bb->minY, $bb->minZ, $bb->maxX, $bb->maxY, $bb->maxZ);

		$this->playerName = $player->getName();
		$this->level = $level->getId();
		$this->center = $center->asVector3();
		$this->hollow = $brush->hollow;
	}

	/**
	 * @param Server $server
	 *
	 * @return Player|null
	 */
	public function getPlayer(Server $server) : ?Player{
		return $server->getPlayer($this->playerName);
	}

	/**
	 * Returns true if the shape is hollow, false if it is not.
	 *
	 * @return bool
	 */
	public function isHollow() : bool{
		return $this->hollow;
	}

	/**
	 * Returns the permission required to use the shape.
	 *
	 * @return string
	 */
	public function getPermission() : string{
		return "blocksniper.shape." . strtolower(ShapeRegistration::getShapeById(self::ID, true));
	}

	/**
	 * @param BaseType    $type
	 * @param Vector2[][] $plotPoints
	 *
	 * @return bool
	 */
	public function editAsynchronously(BaseType $type, array $plotPoints = []) : bool{
		$this->getLevel()->getServer()->getAsyncPool()->submitTask(new BrushTask($this, $type, $this->getTouchedChunks(), $plotPoints));

		return true;
	}

	/**
	 * Returns the level the shape is made in.
	 *
	 * @return Level
	 */
	public function getLevel() : Level{
		return Server::getInstance()->getLevel($this->level);
	}

	/**
	 * @return array
	 */
	public abstract function getTouchedChunks() : array;

	/**
	 * Returns the name of the shape.
	 *
	 * @return string
	 */
	public abstract function getName() : string;

	/**
	 * Returns all blocks in the shape if $partially is false. If true, only returns part of the shape, specified by $blocksPerTick.
	 *
	 * @param $vectorOnly
	 *
	 * @return \Generator
	 */
	public abstract function getBlocksInside(bool $vectorOnly = false) : \Generator;

	/**
	 * Builds a selection if the brush mode is Brush::MODE_BRUSH. $center is the target block, and $bb requires its
	 * bounds to be set.
	 *
	 * @param Vector3       $center
	 * @param Brush         $brush
	 * @param AxisAlignedBB $bb
	 */
	public abstract function buildSelection(Vector3 $center, Brush $brush, AxisAlignedBB $bb) : void;
}
