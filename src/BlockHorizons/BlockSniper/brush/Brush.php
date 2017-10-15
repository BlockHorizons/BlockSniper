<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockSniper\brush;

use BlockHorizons\BlockSniper\brush\registration\ShapeRegistration;
use BlockHorizons\BlockSniper\brush\registration\TypeRegistration;
use BlockHorizons\BlockSniper\events\BrushUseEvent;
use BlockHorizons\BlockSniper\Loader;
use BlockHorizons\BlockSniper\sessions\PlayerSession;
use BlockHorizons\BlockSniper\sessions\Session;
use BlockHorizons\BlockSniper\undo\sync\SyncUndo;
use pocketmine\block\Block;
use pocketmine\block\Sapling;
use pocketmine\item\Item;
use pocketmine\level\generator\biome\Biome;
use pocketmine\level\Position;
use pocketmine\math\Vector2;
use pocketmine\Server;

class Brush implements \JsonSerializable {

	/** @var int */
	public $resetSize = 0;
	/** @var string */
	private $player = "";
	/** @var string */
	private $type = "fill";
	/** @var string */
	private $shape = "sphere";
	/** @var int */
	private $size = 1;
	/** @var bool */
	private $hollow = false;
	/** @var bool */
	private $decrement = false;
	/** @var int */
	private $height = 0;
	/** @var bool */
	private $perfect = true;
	/** @var array */
	private $blocks = [];
	/** @var array */
	private $obsolete = [];
	/** @var string */
	private $biome = "plains";
	/** @var string */
	private $tree = "oak";
	/** @var int */
	private $yOffset = 0;

	public function __construct(string $player) {
		$this->player = $player;
	}

	/**
	 * @return string
	 */
	public function getPlayerName(): string {
		return $this->player;
	}

	/**
	 * @return int
	 */
	public function getYOffset(): int {
		return $this->yOffset;
	}

	/**
	 * @param int $offset
	 */
	public function setYOffset(int $offset): void {
		$this->yOffset = $offset;
	}

	/**
	 * @param bool $value
	 */
	public function setDecrementing(bool $value = true): void {
		$this->decrement = $value;
	}

	/**
	 * @return bool
	 */
	public function isDecrementing(): bool {
		return $this->decrement;
	}

	/**
	 * @return bool
	 */
	public function getPerfect(): bool {
		return $this->perfect;
	}

	/**
	 * @param bool $value
	 */
	public function setPerfect(bool $value = true): void {
		$this->perfect = $value;
	}

	/**
	 * @return Block[]
	 */
	public function getObsolete(): array {
		$data = [];
		foreach($this->obsolete as $block) {
			if(!is_numeric($block)) {
				$data[] = Item::fromString($block)->getBlock();
			} else {
				$data[] = Item::get((int) $block)->getBlock();
			}
		}
		if(empty($data)) {
			return [Block::get(Block::AIR)];
		}
		return $data;
	}

	/**
	 * @param array $blocks
	 */
	public function setObsolete(array $blocks): void {
		$this->obsolete = $blocks;
	}

	/**
	 * @return Block[]
	 */
	public function getBlocks(): array {
		$data = [];
		foreach($this->blocks as $block) {
			if(!is_numeric($block)) {
				$data[] = Item::fromString($block)->getBlock();
			} else {
				$data[] = Item::get((int) $block)->getBlock();
			}
		}
		if(empty($data)) {
			return [Block::get(Block::STONE)];
		}
		return $data;
	}

	/**
	 * @param array $blocks
	 */
	public function setBlocks(array $blocks): void {
		$this->blocks = $blocks;
	}

	/**
	 * @param bool $cloneShape
	 * @param int  $yOffset
	 *
	 * @return BaseShape
	 */
	public function getShape($cloneShape = false, int $yOffset = 0): BaseShape {
		$shapeName = ShapeRegistration::getShape($this->shape);
		$vector3 = Server::getInstance()->getPlayer($this->player)->getTargetBlock(100)->add(0, $yOffset);
		$location = new Position($vector3->x, $vector3->y, $vector3->z, Server::getInstance()->getPlayer($this->player)->getLevel());
		$shape = new $shapeName(Server::getInstance()->getPlayer($this->player), Server::getInstance()->getPlayer($this->player)->getLevel(), $this->size, $location, $this->hollow, $cloneShape);

		return $shape;
	}

	/**
	 * @param string $shape
	 */
	public function setShape(string $shape): void {
		$this->shape = strtolower($shape);
	}

	/**
	 * @return int
	 */
	public function getSize(): int {
		return $this->size;
	}

	/**
	 * @param int $size
	 */
	public function setSize(int $size): void {
		$this->size = $size;
	}

	/**
	 * @return bool
	 */
	public function isHollow(): bool {
		return $this->hollow;
	}

	/**
	 * @param bool $value
	 */
	public function setHollow(bool $value = true): void {
		$this->hollow = $value;
	}

	/**
	 * @return int
	 */
	public function getHeight(): int {
		return $this->height;
	}

	/**
	 * @param int $height
	 */
	public function setHeight(int $height): void {
		$this->height = $height;
	}

	/**
	 * @param array $blocks
	 *
	 * @return BaseType
	 */
	public function getType(array $blocks = []): BaseType {
		$typeName = TypeRegistration::getType($this->type);
		$type = new $typeName(Server::getInstance()->getPlayer($this->player), Server::getInstance()->getPlayer($this->player)->getLevel(), $blocks);

		return $type;
	}

	/**
	 * @param string $type
	 */
	public function setType(string $type): void {
		$this->type = strtolower($type);
	}

	/**
	 * @param mixed $biome
	 */
	public function setBiome($biome): void {
		$this->biome = $biome;
	}

	/**
	 * @return int
	 */
	public function getBiomeId(): int {
		if(is_numeric($this->biome)) {
			return (int) $this->biome;
		}
		$biomes = new \ReflectionClass(Biome::class);
		$const = strtoupper(str_replace(" ", "_", $this->biome));
		if($biomes->hasConstant($const)) {
			$biome = $biomes->getConstant($const);
			return (int) $biome;
		}
		return 0;
	}

	/**
	 * @param $treeType
	 */
	public function setTree($treeType): void {
		$this->tree = $treeType;
	}

	/**
	 * @return int
	 */
	public function getTreeType(): int {
		if(is_numeric($this->tree)) {
			return (int) $this->tree;
		}
		$saplings = new \ReflectionClass(Sapling::class);
		$treeConst = strtoupper(str_replace(" ", "_", $this->tree));
		if($saplings->hasConstant($treeConst)) {
			$treeType = $saplings->getConstant($treeConst);
			return (int) $treeType;
		}
		return 0;
	}

	/**
	 * @return array
	 */
	public function jsonSerialize(): array {
		return [
			$this->size,
			$this->shape,
			$this->type,
			$this->hollow,
			$this->decrement,
			$this->height,
			$this->perfect,
			$this->blocks,
			$this->obsolete,
			$this->biome,
			$this->tree
		];
	}

	/**
	 * @param Session     $session
	 * @param Vector2[][] $plotPoints
	 *
	 * @return bool
	 */
	public function execute(Session $session, array $plotPoints = []): bool {
		$shape = $this->getShape();
		$type = $this->getType();
		if($session instanceof PlayerSession) {
			$player = $session->getSessionOwner()->getPlayer();

			Server::getInstance()->getPluginManager()->callEvent($event = new BrushUseEvent($player, $shape, $type));
			if($event->isCancelled()) {
				return false;
			}
		}
		$this->decrement();

		/** @var Loader $loader */
		$loader = Server::getInstance()->getPluginManager()->getPlugin("BlockSniper");

		if($this->getSize() >= $loader->getSettings()->getMinimumAsynchronousSize() && $type->canBeExecutedAsynchronously()) {
			$shape->editAsynchronously($type, $plotPoints);
		} else {
			$type->setBlocksInside($shape->getBlocksInside());
			$undoBlocks = $type->fillShape($plotPoints);
			$session->getRevertStorer()->saveRevert(new SyncUndo($undoBlocks, $session->getSessionOwner()->getName()));
		}
		return true;
	}

	/**
	 * @return bool
	 */
	public function decrement(): bool {
		if($this->isDecrementing()) {
			if($this->getSize() <= 1) {
				/** @var Loader $loader */
				$loader = Server::getInstance()->getPluginManager()->getPlugin("BlockSniper");
				if($loader->getSettings()->resetDecrementBrush() !== false) {
					$this->setSize($this->resetSize);
					return true;
				}
				return false;
			}
			$this->setSize($this->getSize() - 1);
			return true;
		}
		return false;
	}
}