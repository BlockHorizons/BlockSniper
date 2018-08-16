<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockSniper\brush;

use BlockHorizons\BlockSniper\brush\registration\ShapeRegistration;
use BlockHorizons\BlockSniper\brush\registration\TypeRegistration;
use BlockHorizons\BlockSniper\brush\shapes\SphereShape;
use BlockHorizons\BlockSniper\brush\types\FillType;
use BlockHorizons\BlockSniper\events\BrushUseEvent;
use BlockHorizons\BlockSniper\Loader;
use BlockHorizons\BlockSniper\revert\sync\SyncUndo;
use BlockHorizons\BlockSniper\sessions\PlayerSession;
use BlockHorizons\BlockSniper\sessions\Session;
use pocketmine\block\Block;
use pocketmine\item\Item;
use pocketmine\level\biome\Biome;
use pocketmine\level\generator\object\OakTree;
use pocketmine\level\generator\object\Tree;
use pocketmine\level\Position;
use pocketmine\math\Vector2;
use pocketmine\Server;

class Brush {

	/** @var int */
	public $resetSize = 0;
	/** @var string */
	private $player = "";

	/** @var string */
	public $type;
	/** @var string */
	public $shape;
	/** @var int */
	public $size = 1;
	/** @var bool */
	public $hollow = false;
	/** @var bool */
	public $decrementing = false;
	/** @var int */
	public $height = 0;
	/** @var Block[] */
	public $blocks = [];
	/** @var Block[] */
	public $obsolete = [];
	/** @var Biome */
	public $biome;
	/** @var Tree */
	public $tree;
	/** @var int */
	public $yOffset = 0;

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
	 * @param string $data
	 * @return Block[]
	 */
	public function parseBlocks(string $data): array {
		$blocks = [];
		$fragments = explode(",", $data);
		foreach($fragments as $itemString) {
			if(is_numeric($itemString)) {
				$blocks[] = Item::get((int) $itemString)->getBlock();
			} else {
				$blocks[] = Item::fromString($itemString)->getBlock();
			}
		}
		if(empty($blocks)) {
			$blocks[] = Block::get(Block::AIR);
		}
		return $blocks;
	}

	/**
	 * @param string $data
	 * @return Biome
	 */
	public function parseBiome(string $data): Biome {
		if(is_numeric($data)) {
			return Biome::getBiome((int) $data);
		}
		$biomes = null;
		try {
			$biomes = new \ReflectionClass(Biome::class);
		} catch(\ReflectionException $e) {
		}
		$const = strtoupper(str_replace(" ", "_", $data));
		if($biomes->hasConstant($const)) {
			$biomeId = $biomes->getConstant($const);
			return Biome::getBiome($biomeId);
		}
		return Biome::getBiome(0);
	}

	/**
	 * @param string $data
	 * @return Tree
	 */
	public function parseTree(string $data): Tree {
		try {
			$tree = str_replace("_", "", ucwords($data, "_"));
			$class = "pocketmine\\level\\generator\\object\\" . $tree . "Tree";
			return new $class();
		} catch(\Error $error) {
		}
		return new OakTree();
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
		if($loader === null) {
			return false;
		}

		if($type->canBeExecutedAsynchronously() && $this->size >= $loader->config->AsynchronousOperationSize) {
			$shape->editAsynchronously($type, $plotPoints);
		} else {
			$type->setBlocksInside($shape->getBlocksInside());
			$undoBlocks = $type->fillShape($plotPoints);
			$session->getRevertStorer()->saveRevert(new SyncUndo($undoBlocks, $session->getSessionOwner()->getName()));
		}
		return true;
	}

	/**
	 * @param bool $cloneShape
	 * @param int  $yOffset
	 *
	 * @return BaseShape
	 */
	public function getShape(bool $cloneShape = false, int $yOffset = 0): BaseShape {
		$vector3 = Server::getInstance()->getPlayer($this->player)->getTargetBlock(100)->add(0, $yOffset);

		$location = new Position($vector3->x, $vector3->y, $vector3->z, Server::getInstance()->getPlayer($this->player)->getLevel());
		$shape = new $this->shape(Server::getInstance()->getPlayer($this->player), Server::getInstance()->getPlayer($this->player)->getLevel(), $this->size, $location, $this->hollow, $cloneShape);

		return $shape;
	}

	/**
	 * @param string $data
	 * @return string
	 */
	public function parseShape(string $data): string {
		if(($shape = ShapeRegistration::getShape($data)) === null) {
			return SphereShape::class;
		}
		return $shape;
	}

	/**
	 * @param array $blocks
	 *
	 * @return BaseType
	 */
	public function getType(array $blocks = []): BaseType {
		$type = new $this->type(Server::getInstance()->getPlayer($this->player), Server::getInstance()->getPlayer($this->player)->getLevel(), $blocks);

		return $type;
	}

	/**
	 * @param string $data
	 * @return string
	 */
	public function parseType(string $data): string {
		if(($type = TypeRegistration::getType($data)) === null) {
			return FillType::class;
		}
		return $type;
	}

	public function decrement() {
		if($this->decrementing) {
			if($this->size <= 1) {
				/** @var Loader $loader */
				$loader = Server::getInstance()->getPluginManager()->getPlugin("BlockSniper");
				if($loader === null) {
					return;
				}
				if($loader->config->ResetDecrementBrush) {
					$this->size = $this->resetSize;
				}
				return;
			}
			$this->size = $this->size - 1;
		}
	}
}