<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\brush\registration;

use BlockHorizons\BlockSniper\brush\type\BiomeType;
use BlockHorizons\BlockSniper\brush\type\CleanEntitiesType;
use BlockHorizons\BlockSniper\brush\type\CleanType;
use BlockHorizons\BlockSniper\brush\type\DrainType;
use BlockHorizons\BlockSniper\brush\type\ExpandType;
use BlockHorizons\BlockSniper\brush\type\FillType;
use BlockHorizons\BlockSniper\brush\type\FlattenAllType;
use BlockHorizons\BlockSniper\brush\type\FlattenType;
use BlockHorizons\BlockSniper\brush\type\FreezeType;
use BlockHorizons\BlockSniper\brush\type\HeatType;
use BlockHorizons\BlockSniper\brush\type\LayerType;
use BlockHorizons\BlockSniper\brush\type\LeafBlowerType;
use BlockHorizons\BlockSniper\brush\type\MeltType;
use BlockHorizons\BlockSniper\brush\type\OverlayType;
use BlockHorizons\BlockSniper\brush\type\PlantType;
use BlockHorizons\BlockSniper\brush\type\RegenerateType;
use BlockHorizons\BlockSniper\brush\type\ReplaceAllType;
use BlockHorizons\BlockSniper\brush\type\ReplaceTargetType;
use BlockHorizons\BlockSniper\brush\type\ReplaceType;
use BlockHorizons\BlockSniper\brush\type\SmoothType;
use BlockHorizons\BlockSniper\brush\type\SnowConeType;
use BlockHorizons\BlockSniper\brush\type\TopLayerType;
use BlockHorizons\BlockSniper\brush\type\TreeType;
use BlockHorizons\BlockSniper\brush\type\WarmType;
use BlockHorizons\BlockSniper\data\Translation;
use BlockHorizons\BlockSniper\exception\InvalidIdException;
use pocketmine\permission\Permission;
use pocketmine\permission\PermissionManager;
use ReflectionClass;
use function str_replace;
use function strtolower;

class TypeRegistration{

	/** @var string[] */
	private static $types = [];
	/** @var string[] */
	private static $typesIds = [];

	public static function init() : void{
		self::registerType(BiomeType::class, BiomeType::ID);
		self::registerType(CleanEntitiesType::class, CleanEntitiesType::ID);
		self::registerType(CleanType::class, CleanType::ID);
		self::registerType(DrainType::class, DrainType::ID);
		self::registerType(ExpandType::class, ExpandType::ID);
		self::registerType(FillType::class, FillType::ID);
		self::registerType(FlattenAllType::class, FlattenAllType::ID);
		self::registerType(FlattenType::class, FlattenType::ID);
		self::registerType(LayerType::class, LayerType::ID);
		self::registerType(LeafBlowerType::class, LeafBlowerType::ID);
		self::registerType(MeltType::class, MeltType::ID);
		self::registerType(OverlayType::class, OverlayType::ID);
		self::registerType(ReplaceAllType::class, ReplaceAllType::ID);
		self::registerType(ReplaceType::class, ReplaceType::ID);
		self::registerType(SnowConeType::class, SnowConeType::ID);
		self::registerType(TopLayerType::class, TopLayerType::ID);
		self::registerType(TreeType::class, TreeType::ID);
		self::registerType(RegenerateType::class, RegenerateType::ID);
		self::registerType(FreezeType::class, FreezeType::ID);
		self::registerType(WarmType::class, WarmType::ID);
		self::registerType(HeatType::class, HeatType::ID);
		self::registerType(SmoothType::class, SmoothType::ID);
		self::registerType(ReplaceTargetType::class, ReplaceTargetType::ID);
		self::registerType(PlantType::class, PlantType::ID);
	}

	/**
	 * Registers a new type with the given Class::class as parameter.
	 * Use $overwrite = true if you'd like to overwrite an existing type.
	 *
	 * @param string $class
	 * @param int    $id
	 * @param bool   $overwrite
	 *
	 * @return bool
	 */
	public static function registerType(string $class, int $id, bool $overwrite = false) : bool{
		$shortName = str_replace("Type", "", (new ReflectionClass($class))->getShortName());

		$reflectClass = new ReflectionClass(Translation::class);
		$shortName = Translation::get($reflectClass->getConstant(strtoupper("brush_type_$shortName")));

		if(!$overwrite && self::typeExists(strtolower($shortName), $id)){
			return false;
		}
		if($id < 0){
			throw new InvalidIdException("A shape ID should be positive.");
		}
		self::$typesIds[$id] = $shortName;
		self::$types[strtolower($shortName)] = $class;
		self::registerPermission(strtolower($shortName));

		return true;
	}

	/**
	 * Returns whether a type with the given name exists or not.
	 *
	 * @param string $typeName
	 * @param int    $id
	 *
	 * @return bool
	 */
	public static function typeExists(string $typeName, int $id = -1) : bool{
		return isset(self::$types[$typeName]) || isset(self::$typesIds[$id]);
	}

	/**
	 * @param string $typeName
	 */
	private static function registerPermission(string $typeName) : void{
		$typeName = str_replace(" ", "_", $typeName);
		$permission = new Permission("blocksniper.type." . $typeName, "Allows permission to use the " . $typeName . " shape.", Permission::DEFAULT_OP);
		$permission->addParent("blocksniper.type", true);
		PermissionManager::getInstance()->addPermission($permission);
	}

	/**
	 * Returns an array containing the ID => Name of all types.
	 *
	 * @return string[]
	 */
	public static function getTypeIds() : array{
		return self::$typesIds;
	}

	/**
	 * Returns a type class name by ID.
	 *
	 * @param int  $id
	 * @param bool $name
	 *
	 * @return null|string
	 */
	public static function getTypeById(int $id, bool $name = false) : ?string{
		if(!isset(self::$typesIds[$id])){
			return null;
		}

		return $name ? self::$typesIds[$id] : self::getType(strtolower(self::$typesIds[$id]));
	}

	/**
	 * Returns the class string of the requested type.
	 *
	 * @param string $shortName
	 *
	 * @return null|string
	 */
	public static function getType(string $shortName) : ?string{
		return self::$types[$shortName] ?? null;
	}
}