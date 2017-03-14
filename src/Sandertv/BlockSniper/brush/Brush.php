<?php

namespace Sandertv\BlockSniper\brush;

use pocketmine\block\Block;
use pocketmine\item\Item;
use pocketmine\Player;
use ReflectionClass;
use Sandertv\BlockSniper\Loader;

class Brush {
	
	public static $brush = [];
	public static $owner;
	public static $resetSize = [];
	
	public function __construct(Loader $owner) {
		self::$owner = $owner;
	}
	
	/**
	 * @param Player $player
	 *
	 * @return bool
	 */
	public static function setupDefaultValues(Player $player): bool {
		if(isset(self::$brush[$player->getId()])) {
			return false;
		}
		self::$brush[$player->getId()] = [
			"shape" => "sphere",
			"perfect" => true,
			"type" => "fill",
			"size" => 1,
			"height" => 1,
			"blocks" => [Block::get(Block::STONE)],
			"obsolete" => Block::get(Block::AIR),
			"gravity" => false,
			"decrement" => false,
			"biome" => "plains",
			"hollow" => false
		];
		return true;
	}
	
	/**
	 * @param Player $player
	 * @param array  $blocks
	 */
	public static function setBlocks(Player $player, array $blocks) {
		unset(self::$brush[$player->getId()]["blocks"]);
		foreach($blocks as $block) {
			if(!is_numeric($block)) {
				self::$brush[$player->getId()]["blocks"][] = Item::fromString($block)->getBlock();
			} else {
				self::$brush[$player->getId()]["blocks"][] = Item::get($block)->getBlock();
			}
		}
	}
	
	/**
	 * @param Player $player
	 * @param        $value
	 */
	public static function setDecrementing(Player $player, $value) {
		self::$brush[$player->getId()]["decrement"] = (bool)$value;
	}
	
	/**
	 * @param Player $player
	 *
	 * @return bool
	 */
	public static function isDecrementing(Player $player): bool {
		return self::$brush[$player->getId()]["decrement"];
	}
	
	/**
	 * @param Player $player
	 * @param        $value
	 */
	public static function setGravity(Player $player, $value) {
		self::$brush[$player->getId()]["gravity"] = (bool)$value;
	}
	
	/**
	 * @param Player $player
	 *
	 * @return bool
	 */
	public static function getGravity(Player $player): bool {
		return self::$brush[$player->getId()]["gravity"];
	}
	
	/**
	 * @param Player $player
	 * @param        $value
	 */
	public static function setPerfect(Player $player, $value) {
		self::$brush[$player->getId()]["perfect"] = (bool)$value;
	}
	
	/**
	 * @param Player $player
	 *
	 * @return bool
	 */
	public static function getPerfect(Player $player): bool {
		return self::$brush[$player->getId()]["perfect"];
	}
	
	/**
	 * @param Player $player
	 *
	 * @return Block
	 */
	public static function getObsolete(Player $player): Block {
		return self::$brush[$player->getId()]["obsolete"];
	}
	
	/**
	 * @param Player $player
	 * @param        $block
	 */
	public static function setObsolete(Player $player, $block) {
		self::$brush[$player->getId()]["obsolete"] = (is_numeric($block) ? Item::get($block)->getBlock() : Item::fromString($block)->getBlock());
	}
	
	/**
	 * @param Player $player
	 *
	 * @return array
	 */
	public static function getBlocks(Player $player): array {
		return self::$brush[$player->getId()]["blocks"];
	}
	
	/**
	 * @param Player $player
	 * @param int    $height
	 */
	public static function setHeight(Player $player, int $height) {
		self::$brush[$player->getId()]["height"] = $height;
	}
	
	/**
	 * @param Player $player
	 * @param float  $size
	 */
	public static function setSize(Player $player, float $size) {
		self::$brush[$player->getId()]["size"] = $size;
	}
	
	/**
	 * @param Player $player
	 * @param string $type
	 */
	public static function setType(Player $player, string $type) {
		self::$brush[$player->getId()]["type"] = $type;
	}
	
	/**
	 * @param Player $player
	 * @param string $shape
	 */
	public static function setShape(Player $player, string $shape) {
		self::$brush[$player->getId()]["shape"] = $shape;
	}
	
	/**
	 * @param Player $player
	 *
	 * @return BaseShape
	 */
	public static function getShape(Player $player): BaseShape {
		$hollow = false;
		if(self::getHollow($player) === 1 || self::getHollow($player) === true) {
			$hollow = true;
		}
		$shapeName = 'Sandertv\BlockSniper\brush\shapes\\' . (ucfirst(self::$brush[$player->getId()]["shape"]) . "Shape");
		$shape = new $shapeName(self::$owner, $player, $player->getLevel(), self::getSize($player), $player->getTargetBlock(100), $hollow);
		
		return $shape;
	}
	
	/**
	 * @param Player $player
	 *
	 * @return int
	 */
	public static function getSize(Player $player): int {
		return self::$brush[$player->getId()]["size"];
	}
	
	/**
	 * @param Player $player
	 *
	 * @return int
	 */
	public static function getHeight(Player $player): int {
		return self::$brush[$player->getId()]["height"];
	}
	
	/**
	 * @param Player $player
	 * @param array  $blocks
	 *
	 * @return BaseType
	 */
	public static function getType(Player $player, array $blocks = []): BaseType {
		$typeName = 'Sandertv\BlockSniper\brush\types\\' . (ucfirst(self::$brush[$player->getId()]["type"]) . "Type");
		$type = new $typeName(self::$owner, $player, $player->getLevel(), $blocks);
		
		return $type;
	}
	
	/**
	 * @param Player $player
	 * @param string $biome
	 */
	public static function setBiome(Player $player, string $biome) {
		self::$brush[$player->getId()]["biome"] = $biome;
	}
	
	/**
	 * @param Player $player
	 *
	 * @return int
	 */
	public static function getBiomeId(Player $player): int {
		$biomes = new ReflectionClass('pocketmine\level\generator\biome\Biome');
		$const = strtoupper(str_replace(" ", "_", self::$brush[$player->getId()]["biome"]));
		if($biomes->hasConstant($const)) {
			$biome = $biomes->getConstant($const);
			return $biome;
		}
		return 0;
	}
	
	/**
	 * @param Player $player
	 *
	 * @return bool
	 */
	public static function resetBrush(Player $player): bool {
		if(isset(self::$brush[$player->getId()])) {
			unset(self::$brush[$player->getId()]);
			return true;
		}
		return false;
	}
	
	/**
	 * @param Player $player
	 * @param        $value
	 */
	public static function setHollow(Player $player, $value) {
		self::$brush[$player->getId()]["hollow"] = (bool)$value;
	}
	
	/**
	 * @param Player $player
	 *
	 * @return bool
	 */
	public static function getHollow(Player $player): bool {
		return self::$brush[$player->getId()]["hollow"];
	}
}