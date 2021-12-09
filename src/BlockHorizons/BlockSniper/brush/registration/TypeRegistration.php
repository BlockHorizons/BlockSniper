<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\brush\registration;

use BlockHorizons\BlockSniper\brush\Type;
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
use pocketmine\permission\DefaultPermissions;
use pocketmine\permission\Permission;
use pocketmine\permission\PermissionManager;
use pocketmine\utils\AssumptionFailedError;
use ReflectionClass;
use function str_replace;
use function strtolower;

class TypeRegistration{

	/**
	 * @var string[]
	 * @phpstan-var array<string, class-string<Type>>
	 */
	public static $types = [];

	public static function init() : void{
		self::registerType(BiomeType::class);
		self::registerType(CleanEntitiesType::class);
		self::registerType(CleanType::class);
		self::registerType(DrainType::class);
		self::registerType(ExpandType::class);
		self::registerType(FillType::class);
		self::registerType(FlattenAllType::class);
		self::registerType(FlattenType::class);
		self::registerType(LayerType::class);
		self::registerType(LeafBlowerType::class);
		self::registerType(MeltType::class);
		self::registerType(OverlayType::class);
		self::registerType(ReplaceAllType::class);
		self::registerType(ReplaceType::class);
		self::registerType(SnowConeType::class);
		self::registerType(TopLayerType::class);
		self::registerType(TreeType::class);
		self::registerType(RegenerateType::class);
		self::registerType(FreezeType::class);
		self::registerType(WarmType::class);
		self::registerType(HeatType::class);
		self::registerType(SmoothType::class);
		self::registerType(ReplaceTargetType::class);
		self::registerType(PlantType::class);
	}

	/**
	 * Registers a new type with the given Class::class as parameter.
	 * Use $overwrite = true if you'd like to overwrite an existing type.
	 *
	 * @param string $class
	 * @param bool   $overwrite
	 * @phpstan-param class-string<Type> $class
	 *
	 * @return bool
	 */
	public static function registerType(string $class, bool $overwrite = false) : bool{
		$shortName = str_replace("Type", "", (new ReflectionClass($class))->getShortName());

		$reflectClass = new ReflectionClass(Translation::class);
		$key = $reflectClass->getConstant(strtoupper("brush_type_$shortName"));
		if($key !== false){
			$shortName = Translation::get($key);
		}

		if(!$overwrite && self::typeExists(strtolower($shortName))){
			return false;
		}
		self::$types[strtolower($shortName)] = $class;
		self::registerPermission(strtolower($shortName));

		return true;
	}

	/**
	 * Returns whether a type with the given name exists or not.
	 *
	 * @param string $typeName
	 *
	 * @return bool
	 */
	public static function typeExists(string $typeName) : bool{
		return isset(self::$types[$typeName]);
	}

	/**
	 * @param string $typeName
	 */
	private static function registerPermission(string $typeName) : void{
		$typeName = str_replace(" ", "_", $typeName);
		$operatorPermission = PermissionManager::getInstance()->getPermission(DefaultPermissions::ROOT_OPERATOR) ?? throw new AssumptionFailedError();
		$permission = new Permission("blocksniper.type." . $typeName, "Allows permission to use the " . $typeName . " shape.");
		$operatorPermission->addChild($permission->getName(), true);
		PermissionManager::getInstance()->addPermission($permission);
	}

	/**
	 * Returns an array containing the names of all types.
	 *
	 * @return string[]
	 * @phpstan-return list<string>
	 */
	public static function getTypes() : array{
		$types = [];
		foreach(self::$types as $shortName => $class){
			$types[] = $shortName;
		}

		return $types;
	}

	/**
	 * Returns the class string of the requested type.
	 *
	 * @param string $shortName
	 *
	 * @return null|string
	 * @phpstan-return null|class-string<Type>
	 */
	public static function getType(string $shortName) : ?string{
		return self::$types[$shortName] ?? null;
	}
}