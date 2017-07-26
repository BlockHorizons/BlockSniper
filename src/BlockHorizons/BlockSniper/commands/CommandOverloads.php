<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockSniper\commands;

use BlockHorizons\BlockSniper\brush\BrushParameters;
use BlockHorizons\BlockSniper\Loader;

class CommandOverloads {

	/** @var array */
	private static $commandOverloads = [];

	/**
	 * @param string $blockSniperCommand
	 *
	 * @return array
	 */
	public static function getOverloads(string $blockSniperCommand): array {
		return self::$commandOverloads[$blockSniperCommand];
	}

	public static function initialize() {
		self::$commandOverloads = [
			"brush" => [
				0 => [
					"type" => "stringenum",
					"name" => "parameter",
					"optional" => false,
					"enum_values" => BrushParameters::getAliasesAndParameters()
				],
				1 => [
					"type" => "rawtext",
					"name" => "value",
					"optional" => true
				],
				2 => [
					"type" => "string",
					"name" => "name",
					"optional" => true
				]
			],

			"blocksniper" => [
				0 => [
					"type" => "stringenum",
					"name" => "parameter",
					"optional" => true,
					"enum_values" => [
						"language",
						"reload"
					]
				],
				1 => [
					"type" => "stringenum",
					"name" => "language",
					"optional" => true,
					"enum_values" => Loader::getAvailableLanguages()
				]
			],

			"clone" => [
				0 => [
					"type" => "stringenum",
					"name" => "type",
					"optional" => false,
					"enum_values" => [
						"copy",
						"template",
						"schematic",
						"offset"
					]
				],
				1 => [
					"type" => "rawtext",
					"name" => "name",
					"optional" => true
				]
			],

			"paste" => [
				0 => [
					"type" => "stringenum",
					"name" => "type",
					"optional" => false,
					"enum_values" => [
						"copy",
						"template",
						"schematic"
					]
				],
				1 => [
					"type" => "rawtext",
					"name" => "name",
					"optional" => true
				]
			],

			"redo" => [
				0 => [
					"type" => "int",
					"name" => "amount",
					"optional" => true
				]
			],

			"undo" => [
				0 => [
					"type" => "int",
					"name" => "amount",
					"optional" => true
				]
			],

			"cancel" => [

			]
		];
	}
}