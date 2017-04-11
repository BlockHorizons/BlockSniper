<?php

namespace BlockHorizons\BlockSniper\brush;

use pocketmine\block\Block;
use pocketmine\item\Item;
use pocketmine\Server;

class Brush {
	
	public $player;
	public $resetSize = 0;
	private $type = "fill", $shape = "sphere", $size = 1, $hollow = false, $decrement = false;
	private $height = 1, $perfect = true, $blocks = [], $obsolete = [], $biome = "plains", $tree = "oak";
	
	public function __construct(string $player) {
		$this->player = $player;
	}
	
	/**
	 * @param array $blocks
	 */
	public function setBlocks(array $blocks) {
		$this->blocks = $blocks;
	}
	
	/**
	 * @param $value
	 */
	public function setDecrementing($value) {
		$this->decrement = (bool)$value;
	}
	
	/**
	 * @return bool
	 */
	public function isDecrementing(): bool {
		return $this->decrement;
	}
	
	/**
	 * @param $value
	 */
	public function setPerfect($value) {
		$this->perfect = (bool)$value;
	}
	
	/**
	 * @return bool
	 */
	public function getPerfect(): bool {
		return $this->perfect;
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
				$data[] = Item::get($block)->getBlock();
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
	public function setObsolete(array $blocks) {
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
				$data[] = Item::get($block)->getBlock();
			}
		}
		if(empty($data)) {
			return [Block::get(Block::STONE)];
		}
		return $data;
	}
	
	/**
	 * @param float $size
	 */
	public function setSize(float $size) {
		$this->size = $size;
	}
	
	/**
	 * @param string $shape
	 */
	public function setShape(string $shape) {
		$this->shape = $shape;
	}
	
	/**
	 * @return BaseShape
	 */
	public function getShape(): BaseShape {
		$shapeName = 'BlockHorizons\BlockSniper\brush\shapes\\' . (ucfirst($this->shape) . "Shape");
		$shape = new $shapeName(Server::getInstance()->getPlayer($this->player), Server::getInstance()->getPlayer($this->player)->getLevel(), $this->size, Server::getInstance()->getPlayer($this->player)->getTargetBlock(100), $this->hollow);
		
		return $shape;
	}
	
	/**
	 * @return int
	 */
	public function getSize(): int {
		return $this->size;
	}
	
	/**
	 * @return bool
	 */
	public function getHollow(): bool {
		return $this->hollow;
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
	public function setHeight(int $height) {
		$this->height = $height;
	}
	
	/**
	 * @param array $blocks
	 *
	 * @return BaseType
	 */
	public function getType(array $blocks = []): BaseType {
		$typeName = 'BlockHorizons\BlockSniper\brush\types\\' . (ucfirst($this->type) . "Type");
		$type = new $typeName(Server::getInstance()->getPluginManager()->getPlugin("BlockSniper")->getUndoStore(), Server::getInstance()->getPlayer($this->player), Server::getInstance()->getPlayer($this->player)->getLevel(), $blocks);
		
		return $type;
	}
	
	/**
	 * @param string $type
	 */
	public function setType(string $type) {
		$this->type = $type;
	}
	
	/**
	 * @param mixed $biome
	 */
	public function setBiome($biome) {
		$this->biome = $biome;
	}
	
	/**
	 * @return int
	 */
	public function getBiomeId(): int {
		if(is_numeric($this->biome)) {
			return $this->biome;
		}
		$biomes = new \ReflectionClass('pocketmine\level\generator\biome\Biome');
		$const = strtoupper(str_replace(" ", "_", $this->biome));
		if($biomes->hasConstant($const)) {
			$biome = $biomes->getConstant($const);
			return $biome;
		}
		return 0;
	}
	
	/**
	 * @param $value
	 */
	public function setHollow($value) {
		$this->hollow = (bool)$value;
	}
	
	/**
	 * @param $treeType
	 */
	public function setTree($treeType) {
		$this->tree = $treeType;
	}
	
	/**
	 * @return int
	 */
	public function getTreeType(): int {
		if(is_numeric($this->tree)) {
			return $this->tree;
		}
		$saplings = new \ReflectionClass('pocketmine\block\Sapling');
		$treeConst = strtoupper(str_replace(" ", "_", $this->tree));
		if($saplings->hasConstant($treeConst)) {
			$treeType = $saplings->getConstant($treeConst);
			return $treeType;
		}
		return 0;
	}
}