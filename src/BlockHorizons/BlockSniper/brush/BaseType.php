<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockSniper\brush;

use BlockHorizons\BlockSniper\brush\async\BlockSniperChunkManager;
use BlockHorizons\BlockSniper\sessions\SessionManager;
use pocketmine\block\Block;
use pocketmine\level\ChunkManager;
use pocketmine\level\format\Chunk;
use pocketmine\level\Level;
use pocketmine\level\Position;
use pocketmine\Player;
use pocketmine\Server;

abstract class BaseType {

	const ID = -1;

	const TYPE_BIOME = 0;
	const TYPE_CLEAN_ENTITIES = 1;
	const TYPE_CLEAN = 2;
	const TYPE_DRAIN = 3;
	const TYPE_EXPAND = 4;
	const TYPE_FILL = 5;
	const TYPE_FLATTEN_ALL = 6;
	const TYPE_FLATTEN = 7;
	const TYPE_LAYER = 8;
	const TYPE_LEAF_BLOWER = 9;
	const TYPE_MELT = 10;
	const TYPE_OVERLAY = 11;
	const TYPE_REPLACE_ALL = 12;
	const TYPE_REPLACE = 13;
	const TYPE_SNOW_CONE = 14;
	const TYPE_TOP_LAYER = 15;
	const TYPE_TREE = 16;
	const TYPE_REGENERATE = 17;

	/** @var int */
	protected $level = 0;
	/** @var int */
	protected $biome = 0;
	/** @var Block[] */
	protected $blocks = [];
	/** @var Position|null */
	protected $center = null;
	/** @var Block[]|array */
	protected $obsolete = [];
	/** @var int */
	protected $tree = 0;
	/** @var Block[] */
	protected $brushBlocks = [];
	/** @var int */
	protected $height = 0;
	/** @var null|BlockSniperChunkManager */
	protected $chunkManager = null;
	/** @var bool */
	private $async = false;
	/** @var int */
	protected $id = -1;

	/**
	 * @param Player       $player
	 * @param ChunkManager $manager
	 * @param Block[]      $blocks
	 */
	public function __construct(Player $player, ChunkManager $manager, array $blocks) {
		if($manager instanceof Level) {
			$this->level = $manager->getId();
		} else {
			$this->async = true;
			$this->chunkManager = $manager;
		}
		$this->blocks = $blocks;
		$this->brushBlocks = SessionManager::getPlayerSession($player)->getBrush()->getBlocks();
	}

	/**
	 * @param string $type
	 *
	 * @return bool
	 */
	public static function isType(string $type): bool {
		$typeConst = strtoupper("type_" . $type);
		if(defined("self::$typeConst")) {
			return true;
		}
		return false;
	}

	/**
	 * @return array
	 */
	public static function getTypes(): array {
		return [
			"Biome",
			"CleanEntities",
			"Clean",
			"Drain",
			"Expand",
			"Fill",
			"FlattenAll",
			"Flatten",
			"Layer",
			"LeafBlower",
			"Melt",
			"Overlay",
			"ReplaceAll",
			"Replace",
			"Snowcone",
			"TopLayer",
			"Tree"
		];
	}

	/**
	 * Registers a new Type. Example:
	 * Raise, 12
	 *
	 * Defines the type as a constant making it able to be used.
	 *
	 *
	 * @param string $type
	 * @param int    $number
	 *
	 * @return bool
	 */
	public static function registerType(string $type, int $number): bool {
		$typeConst = strtoupper("type_" . str_replace("_", "", $type));
		if(defined("self::$typeConst")) {
			return false;
		}
		define('BlockHorizons\BlockSniper\brush\BaseType\\' . $typeConst, $number);
		return true;
	}

	/**
	 * @param Chunk[] $chunks
	 *
	 * @return BlockSniperChunkManager
	 */
	public static function establishChunkManager(array $chunks): BlockSniperChunkManager {
		$manager = new BlockSniperChunkManager(0);
		foreach($chunks as $chunk) {
			$manager->setChunk($chunk->getX(), $chunk->getZ(), $chunk);
		}
		return $manager;
	}

	/**
	 * @return Block[]|null
	 */
	public final function fillShape(): ?array {
		if($this->isAsynchronous() && $this->canBeExecutedAsynchronously()) {
			$this->fillAsynchronously();
			return null;
		}
		return $this->fillSynchronously();
	}

	/**
	 * @return Block[]
	 */
	public abstract function fillSynchronously(): array;

	public function fillAsynchronously(): void {
		return;
	}

	/**
	 * Returns the level the type is used in.
	 *
	 * @return Level|null
	 */
	public function getLevel(): ?Level {
		return Server::getInstance()->getLevel($this->level);
	}

	/**
	 * @return int
	 */
	public function getId(): int {
		return $this->id;
	}

	/**
	 * @return bool
	 */
	public function canBeExecutedAsynchronously(): bool {
		return true;
	}

	/**
	 * @return int
	 */
	public function getLevelId(): int {
		return $this->level;
	}

	/**
	 * @param array $blocks
	 *
	 * @return BaseType
	 */
	public function setBlocksInside(array $blocks): self {
		$this->blocks = $blocks;

		return $this;
	}

	/**
	 * Returns the blocks the type is being executed upon.
	 *
	 * @return array
	 */
	public function getBlocks(): array {
		return $this->blocks;
	}

	/**
	 * @return array
	 */
	public function getBrushBlocks(): array {
		return $this->brushBlocks;
	}

	/**
	 * Returns the permission required to use the type.
	 *
	 * @return string
	 */
	public function getPermission(): string {
		return "blocksniper.type." . str_replace("hollow", "", str_replace(" ", "_", strtolower($this->getName())));
	}

	/**
	 * @return string
	 */
	public abstract function getName(): string;

	/**
	 * @return bool
	 */
	public function isAsynchronous(): bool {
		return $this->async;
	}

	/**
	 * @param bool $value
	 *
	 * @return BaseType
	 */
	public function setAsynchronous(bool $value = true): self {
		$this->async = $value;

		return $this;
	}

	/**
	 * @return BlockSniperChunkManager
	 */
	public function getChunkManager(): BlockSniperChunkManager {
		return $this->chunkManager;
	}

	/**
	 * @param ChunkManager $manager
	 *
	 * @return BaseType
	 */
	public function setChunkManager(ChunkManager $manager): self {
		$this->chunkManager = $manager;

		return $this;
	}
}
