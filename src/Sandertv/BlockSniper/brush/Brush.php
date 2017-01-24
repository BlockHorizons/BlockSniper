<?php

namespace Sandertv\BlockSniper\brush;

use Sandertv\BlockSniper\Loader;
use pocketmine\Player;
use pocketmine\item\Item;
use pocketmine\block\Block;

use Sandertv\BlockSniper\brush\shapes\CubeShape;
use Sandertv\BlockSniper\brush\shapes\CuboidShape;
use Sandertv\BlockSniper\brush\shapes\CylinderStandingShape;
use Sandertv\BlockSniper\brush\shapes\SphereShape;

use Sandertv\BlockSniper\brush\types\FillType;
use Sandertv\BlockSniper\brush\types\CleanType;
use Sandertv\BlockSniper\brush\types\DrainType;
use Sandertv\BlockSniper\brush\types\FlattenType;
use Sandertv\BlockSniper\brush\types\LayerType;
use Sandertv\BlockSniper\brush\types\LeafBlowerType;
use Sandertv\BlockSniper\brush\types\OverlayType;
use Sandertv\BlockSniper\brush\types\ReplaceType;

class Brush {
	
	public static $brush = [];
	public static $owner;
	
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
			"blocks" => [Block::get(Block::AIR)],
			"obsolete" => Block::get(Block::AIR)
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
	
	public static function setPerfect(Player $player, $value) {
		self::$brush[$player->getId()]["perfect"] = (bool) $value;
	}
	
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
	 * @param float  $size
	 */
	public static function setHeight(Player $player, int $height) {
		self::$brush[$player->getId()]["size"] = $height;
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
	 * @param float  $size
	 */
	public static function setSize(Player $player, float $size) {
		self::$brush[$player->getId()]["size"] = $size;
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
		$shapeName = self::$brush[$player->getId()]["shape"];
		switch($shapeName) {
			case "cube":
				$shape = new CubeShape(self::$owner, $player->getLevel(), self::getSize($player), $player->getTargetBlock(100));
				break;
			case "sphere":
				$shape = new SphereShape(self::$owner, $player, $player->getLevel(), self::getSize($player), $player->getTargetBlock(100));
				break;
			case "cuboid":
				$shape = new CuboidShape(self::$owner, $player->getLevel(), self::getSize($player), self::getHeight($player), $player->getTargetBlock(100));
				break;
			case "cylinder":
				$shape = new CylinderStandingShape(self::$owner, $player->getLevel(), self::getSize($player), self::getHeight($player), $player->getTargetBlock(100));
				break;
				
			default:
				$shape = new SphereShape(self::$owner, $player->getLevel(), self::getSize($player), $player->getTargetBlock(100));
				break;
		}
		return $shape;
	}
	
	/**
	 * @param Player $player
	 * @param array  $blocks
	 *
	 * @return BaseType
	 */
	public static function getType(Player $player, array $blocks): BaseType {
		$typeName = self::$brush[$player->getId()]["type"];
		switch($typeName) {
			case "fill":
				$shape = new FillType(self::$owner, $player, $player->getLevel(), $blocks);
				break;
			case "clean":
				$shape = new CleanType(self::$owner, $player, $player->getLevel(), $blocks);
				break;
			case "drain":
				$shape = new DrainType(self::$owner, $player, $player->getLevel(), $blocks);
				break;
			case "flatten":
				$shape = new FlattenType(self::$owner, $player, $player->getLevel(), $blocks, $player->getTargetBlock(100));
				break;
			case "layer":
				$shape = new LayerType(self::$owner, $player, $player->getLevel(), $blocks, $player->getTargetBlock(100));
				break;
			case "leafblower":
				$shape = new LeafBlowerType(self::$owner, $player, $player->getLevel(), $blocks);
				break;
			case "overlay":
				$shape = new OverlayType(self::$owner, $player, $player->getLevel(), $blocks);
				break;
			case "replace":
				$shape = new ReplaceType(self::$owner, $player, $player->getLevel(), $blocks);
				break;
		}
		return $shape;
	}
}