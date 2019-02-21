<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\parser;

use function json_decode;
use function stream_get_contents;

class IdMap{

	/** @var string[] */
	public static $ids = [];

	/**
	 * IdMap constructor.
	 *
	 * @param resource $file
	 */
	public function __construct($file){
		self::$ids = json_decode(stream_get_contents($file), true);
	}

	/**
	 * translate attempts to translate the string passed in. If not successful, null is returned.
	 *
	 * @param string $id
	 *
	 * @return string|null
	 */
	public static function translate(string $id) : ?string {
		return self::$ids[$id] ?? null;
	}
}