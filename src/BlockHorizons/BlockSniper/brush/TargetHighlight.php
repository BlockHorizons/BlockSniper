<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\brush;

use BlockHorizons\BlockSniper\iterator\BlockEdgeIterator;
use pocketmine\block\VanillaBlocks;
use pocketmine\entity\Human;
use pocketmine\entity\Location;
use pocketmine\entity\Skin;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\FloatTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\world\Position;
use Ramsey\Uuid\Uuid;
use function chr;
use function json_encode;
use function round;
use function str_repeat;
use const JSON_THROW_ON_ERROR;

/**
 * Class TargetHighlight is an entity extending Human to implement a highlight of a target block. It forms a frame
 * around a block when placed at the correct position.
 */
class TargetHighlight extends Human{

	/** @var string */
	private static $geometry = "";
	/** @var string */
	private static $emptySkin = "";

	/**
	 * GEOMETRY_NAME is the name of the geometry structure.
	 */
	private const GEOMETRY_NAME = "geometry.target_highlight3";

	/**
	 * width and height are set to 0 to prevent any collision checks.
	 *
	 * @var float
	 */
	public $width = 0.0, $height = 0.0;

	public function __construct(Position $position){
		if(self::$emptySkin === ""){
			self::$emptySkin = str_repeat(chr(0) . chr(0) . chr(0) . chr(255), 32 * 64);
		}
		$this->skin = new Skin(
			Uuid::uuid4()->toString(),
			self::$emptySkin,
			"",
			self::GEOMETRY_NAME,
			$this->generateGeometry()
		);

		$nbt = new CompoundTag();
		$nbt->setTag("Pos", new ListTag([
					new FloatTag($position->x),
					new FloatTag($position->y),
					new FloatTag($position->z)
				]
			)
		);
		$nbt->setTag("Rotation", new ListTag([
					new FloatTag(0),
					new FloatTag(0)
				]
			)
		);
		parent::__construct(Location::fromObject($position, $position->getWorld(), 0.0, 0.0), $this->skin, $nbt);

		$this->setCanSaveWithChunk(false);

		// We set the scale slightly bigger than normal so that it extends out of the block just a little bit.
		$this->setScale(1.03);
	}

	// Empty stubs to keep the entity from moving and being attacked.
	public function attack(EntityDamageEvent $source) : void{
	}

	public function onUpdate(int $currentTick) : bool{
		return false;
	}

	/**
	 * generateGeometry generates the JSON encoded geometry structure required to produce a frame around a block.
	 *
	 * @return string
	 */
	private function generateGeometry() : string{
		if(self::$geometry !== ""){
			return self::$geometry;
		}

		$cubes = [];
		foreach((new BlockEdgeIterator(VanillaBlocks::AIR()))->getEdges() as $edge){
			foreach($edge->walk(1.0 / 16.0) as $pos){
				$cubes[] = [
					"origin" => [(int) round($pos->x * 16), (int) round($pos->y * 16), (int) round($pos->z * 16)],
					"size" => [1, 1, 1],
					"uv" => [0, 0]
				];
			}
		}

		self::$geometry = json_encode([self::GEOMETRY_NAME => [
				"bones" => [
					[
						// The 'head' name here is required. The head appears to be the only component that doesn't move
						// client-side when inside of a block.
						"name" => "head",
						"pivot" => [0, 0, 0],
						"cubes" => $cubes
					]
				]
			]
			], JSON_THROW_ON_ERROR
		);

		return self::$geometry;
	}
}