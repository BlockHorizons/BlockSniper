<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockSniper\brush;

use BlockHorizons\BlockSniper\brush\registration\ShapeRegistration;
use BlockHorizons\BlockSniper\brush\registration\TypeRegistration;
use BlockHorizons\BlockSniper\brush\shapes\SphereShape;
use BlockHorizons\BlockSniper\brush\types\FillType;
use pocketmine\block\Block;
use pocketmine\item\Item;
use pocketmine\level\biome\Biome;
use pocketmine\level\generator\object\Tree;

class BrushProperties implements \JsonSerializable {

	/** @var string */
	public $type = FillType::class;
	/** @var string */
	public $shape = SphereShape::class;
	/** @var int */
	public $size = 1;
	/** @var bool */
	public $hollow = false;
	/** @var bool */
	public $decrementing = false;
	/** @var int */
	public $height = 0;
	/** @var string */
	public $blocks = [];
	/** @var string */
	public $obsolete = [];
	/** @var int */
	public $biomeId = 1;
	/** @var int */
	public $tree = 1;

	public function jsonSerialize(): array {
		return (array) $this;
	}

	/**
	 * @param string $data
	 * @return Block[]
	 */
	private function parseBlocks(string $data): array {
		$blocks = [];
		$fragments = explode(",", $data);
		foreach($fragments as $itemString) {
			if($itemString === "") {
				continue;
			}
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
	 * @return Block[]
	 */
	public function getBlocks(): array {
		return $this->parseBlocks($this->blocks);
	}

	/**
	 * @return Block[]
	 */
	public function getObsolete(): array {
		return $this->parseBlocks($this->obsolete);
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
	 * @param string $data
	 * @return int
	 */
	public function parseBiomeId(string $data): int {
		if(is_numeric($data)) {
			return (int) $data;
		}
		$biomes = null;
		try {
			$biomes = new \ReflectionClass(Biome::class);
		} catch(\ReflectionException $e) {
		}
		$const = strtoupper(str_replace(" ", "_", $data));
		if($biomes->hasConstant($const)) {
			return $biomes->getConstant($const);
		}
		return 0;
	}

	/**
	 * @param string $data
	 * @return int
	 */
	public function parseTreeId(string $data): int {
		try {
			$tree = str_replace("_", "", ucwords($data, "_"));
			$class = "pocketmine\\level\\generator\\object\\" . $tree . "Tree";
			/** @var Tree $c */
			$c = new $class();
			return $c->type;
		} catch(\Error $error) {
		}
		return 0;
	}
}