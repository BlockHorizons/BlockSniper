<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\brush;

use BlockHorizons\BlockSniper\brush\async\tasks\BrushTask;
use BlockHorizons\BlockSniper\brush\registration\ShapeRegistration;
use pocketmine\level\Level;
use pocketmine\level\Position;
use pocketmine\math\Vector2;
use pocketmine\math\Vector3;
use pocketmine\Player;
use pocketmine\Server;

abstract class BaseShape{

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
	/** @var int */
	protected $height = 0;
	/** @var int */
	protected $radius = 0;
	/** @var int */
	protected $width = 0;
	/** @var string */
	protected $playerName = "";

	public function __construct(Player $player, Level $level, Position $center, bool $hollow){
		$this->playerName = $player->getName();
		$this->level = $level->getId();
		$this->center = $center->asVector3();
		$this->hollow = $hollow;
	}

	/**
	 * Returns all blocks in the shape if $partially is false. If true, only returns part of the shape, specified by $blocksPerTick.
	 *
	 * @param $vectorOnly
	 *
	 * @return \Generator
	 */
	public abstract function getBlocksInside(bool $vectorOnly = false) : \Generator;

	/**
	 * Returns the approximate amount of processed blocks in the shape. This may not be perfectly accurate.
	 *
	 * @return int
	 */
	public abstract function getApproximateProcessedBlocks() : int;

	/**
	 * @param Server $server
	 *
	 * @return Player|null
	 */
	public function getPlayer(Server $server) : ?Player{
		return $server->getPlayer($this->playerName);
	}

	/**
	 * @return int
	 */
	public function getLevelId() : int{
		return $this->level;
	}

	/**
	 * Returns the center of the shape made, or the target block.
	 *
	 * @return Position
	 */
	public function getCenter() : Position{
		return Position::fromObject($this->center, Server::getInstance()->getLevel($this->level));
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
	 * Returns the name of the shape.
	 *
	 * @return string
	 */
	public abstract function getName() : string;

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
	 * @param int $targetX
	 * @param int $targetY
	 * @param int $targetZ
	 * @param int $width
	 * @param int $height
	 *
	 * @return array
	 */
	protected function calculateBoundaryBlocks(int $targetX, int $targetY, int $targetZ, int $width, int $height) : array{
		$minX = $targetX - $width;
		$minZ = $targetZ - $width;
		$minY = $targetY - $height;
		$maxX = $targetX + $width;
		$maxZ = $targetZ + $width;
		$maxY = $targetY + $height;

		return [$minX, $minY, $minZ, $maxX, $maxY, $maxZ];
	}

	/**
	 * @param Vector3 $vector
	 *
	 * @return array
	 */
	protected function arrayVec(Vector3 $vector) : array{
		return [$vector->x, $vector->y, $vector->z];
	}
}
