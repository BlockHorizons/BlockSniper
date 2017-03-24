<?php

namespace Sandertv\BlockSniper\brush;

use pocketmine\block\Block;
use pocketmine\item\Item;
use pocketmine\Player;
use Sandertv\BlockSniper\Loader;

class Brush {
	
	public $player;
	public $resetSize = 0;
	private $plugin;
	private $type = "fill", $shape = "sphere", $size = 1, $hollow = false, $decrement = false;
	private $height = 1, $perfect = true, $blocks = [], $obsolete = [], $biome = "plains";
	
	public function __construct(string $player, Loader $plugin) {
		$this->player = $player;
		$this->plugin = $plugin;
		
		$this->blocks = [Block::get(Block::STONE)];
		$this->obsolete = [Block::get(Block::AIR)];
	}
	
	/**
	 * @param Player $player
	 * @param array  $blocks
	 */
	public function setBlocks(array $blocks) {
		unset($this->blocks);
		foreach($blocks as $block) {
			if(!is_numeric($block)) {
				$this->blocks[] = Item::fromString($block)->getBlock();
			} else {
				$this->blocks[] = Item::get($block)->getBlock();
			}
		}
	}
	
	/**
	 * @param Player $player
	 * @param        $value
	 */
	public function setDecrementing($value) {
		$this->decrement = (bool)$value;
	}
	
	/**
	 * @param Player $player
	 *
	 * @return bool
	 */
	public function isDecrementing(): bool {
		return $this->decrement;
	}
	
	/**
	 * @param Player $player
	 * @param        $value
	 */
	public function setPerfect($value) {
		$this->perfect = (bool)$value;
	}
	
	/**
	 * @param Player $player
	 *
	 * @return bool
	 */
	public function getPerfect(): bool {
		return $this->perfect;
	}
	
	/**
	 * @param Player $player
	 *
	 * @return array
	 */
	public function getObsolete(): array {
		return $this->obsolete;
	}
	
	/**
	 * @param Player $player
	 * @param array  $blocks
	 */
	public function setObsolete(array $blocks) {
		unset($this->obsolete);
		foreach($blocks as $block) {
			if(!is_numeric($block)) {
				$this->obsolete[] = Item::fromString($block)->getBlock();
			} else {
				$this->obsolete[] = Item::get($block)->getBlock();
			}
		}
	}
	
	/**
	 * @return array
	 */
	public function getBlocks(): array {
		return $this->blocks;
	}
	
	/**
	 * @param float  $size
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
		$shapeName = 'Sandertv\BlockSniper\brush\shapes\\' . (ucfirst($this->shape) . "Shape");
		$shape = new $shapeName($this->plugin->getServer()->getPlayer($this->player), $this->plugin->getServer()->getPlayer($this->player)->getLevel(), $this->size, $this->plugin->getServer()->getPlayer($this->player)->getTargetBlock(100), $this->hollow);
		
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
	 * @param int    $height
	 */
	public function setHeight(int $height) {
		$this->height = $height;
	}
	
	/**
	 * @param array  $blocks
	 *
	 * @return BaseType
	 */
	public function getType(array $blocks = []): BaseType {
		$typeName = 'Sandertv\BlockSniper\brush\types\\' . (ucfirst($this->type) . "Type");
		$type = new $typeName($this->plugin->getServer()->getPluginManager()->getPlugin("BlockSniper"), $this->plugin->getServer()->getPlayer($this->player), $this->plugin->getServer()->getPlayer($this->player)->getLevel(), $blocks);
		
		return $type;
	}
	
	/**
	 * @param string $type
	 */
	public function setType(string $type) {
		$this->type = $type;
	}
	
	/**
	 * @param mixed  $biome
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
	 * @param        $value
	 */
	public function setHollow($value) {
		$this->hollow = (bool)$value;
	}
}