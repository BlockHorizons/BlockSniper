<?php

namespace BlockHorizons\BlockSniper\brush;


class BrushParameters {

	const BRUSH_PARAMETERS = [
		"reset" => ["re"],
		"preset" => ["pr"],
		"type" => ["ty"],
		"shape" => ["sh"],
		"size" => ["si"],
		"hollow" => ["ho"],
		"decrement" => ["de", "decrementing"],
		"obsolete" => ["ob", "replaced"],
		"perfect" => ["pe"],
		"biome" => ["bi"],
		"tree" => ["tr"],
		"blocks" => ["bl", "block"],
		"height" => ["he"]
	];

	/**
	 * @param string $parameter
	 *
	 * @return array
	 */
	public static function getAliases(string $parameter): array {
		return self::BRUSH_PARAMETERS[$parameter];
	}

	/**
	 * @param string $attempt
	 *
	 * @return string
	 */
	public static function matchesParameter(string $attempt): string {
		if(defined(self::BRUSH_PARAMETERS[strtolower($attempt)])) {
			return $attempt;
		}
		foreach(self::BRUSH_PARAMETERS as $key => $parameter) {
			foreach(self::BRUSH_PARAMETERS[$key] as $alias) {
				if(strtolower($attempt) === $alias) {
					return $parameter;
				}
			}
		}
		return null;
	}

	/**
	 * @return array
	 */
	public static function getAliasesAndParameters(): array {
		$fullList = [];
		foreach(self::BRUSH_PARAMETERS as $key => $parameters) {
			$fullList[] = $key;
			foreach(self::BRUSH_PARAMETERS[$key] as $alias) {
				$fullList[] = $alias;
			}
		}
		return $fullList;
	}
}