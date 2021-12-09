<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\brush\registration;

use BlockHorizons\BlockSniper\brush\Shape;
use BlockHorizons\BlockSniper\brush\shape\CubeShape;
use BlockHorizons\BlockSniper\brush\shape\CuboidShape;
use BlockHorizons\BlockSniper\brush\shape\CylinderShape;
use BlockHorizons\BlockSniper\brush\shape\EllipsoidShape;
use BlockHorizons\BlockSniper\brush\shape\SphereShape;
use BlockHorizons\BlockSniper\data\Translation;
use pocketmine\permission\DefaultPermissions;
use pocketmine\permission\Permission;
use pocketmine\permission\PermissionManager;
use pocketmine\utils\AssumptionFailedError;
use ReflectionClass;
use function str_replace;
use function strtolower;

class ShapeRegistration{

	/**
	 * @var string[]
	 * @phpstan-var array<string, class-string<Shape>>
	 */
	public static $shapes = [];

	public static function init() : void{
		self::registerShape(SphereShape::class);
		self::registerShape(CubeShape::class);
		self::registerShape(CuboidShape::class);
		self::registerShape(CylinderShape::class);
		self::registerShape(EllipsoidShape::class);
	}

	/**
	 * Registers a new shape with the given Class::class as parameter.
	 * Use $overwrite = true if you'd like to overwrite an existing shape.
	 *
	 * @param string $class
	 * @param bool   $overwrite
	 * @phpstan-param class-string<Shape> $class
	 *
	 * @return bool
	 */
	public static function registerShape(string $class, bool $overwrite = false) : bool{
		$shortName = str_replace("Shape", "", (new ReflectionClass($class))->getShortName());

		$reflectClass = new ReflectionClass(Translation::class);
		$key = $reflectClass->getConstant(strtoupper("brush_shape_$shortName"));
		if($key !== false){
			$shortName = Translation::get($key);
		}

		if(!$overwrite && self::shapeExists(strtolower($shortName))){
			return false;
		}
		self::$shapes[strtolower($shortName)] = $class;
		self::registerPermission(strtolower($shortName));

		return true;
	}

	/**
	 * Returns whether a shape with the given name exists or not.
	 *
	 * @param string $shapeName
	 *
	 * @return bool
	 */
	public static function shapeExists(string $shapeName) : bool{
		return isset(self::$shapes[$shapeName]);
	}

	/**
	 * @param string $shapeName
	 */
	private static function registerPermission(string $shapeName) : void{
		$shapeName = str_replace(" ", "_", $shapeName);

		$operatorPermission = PermissionManager::getInstance()->getPermission(DefaultPermissions::ROOT_OPERATOR) ?? throw new AssumptionFailedError();
		$permission = new Permission("blocksniper.shape." . $shapeName, "Allows permission to use the " . $shapeName . " shape.");
		$operatorPermission->addChild($permission->getName(), true);
		PermissionManager::getInstance()->addPermission($permission);
	}

	/**
	 * Returns an array containing the names of all shapes.
	 *
	 * @return string[]
	 * @phpstan-return list<string>
	 */
	public static function getShapes() : array{
		$shapes = [];
		foreach(self::$shapes as $shortName => $class){
			$shapes[] = $shortName;
		}

		return $shapes;
	}

	/**
	 * Returns the class string of the requested shape.
	 *
	 * @param string $shortName
	 *
	 * @return null|string
	 * @phpstan-return null|class-string<Shape>
	 */
	public static function getShape(string $shortName) : ?string{
		return self::$shapes[strtolower($shortName)] ?? null;
	}
}