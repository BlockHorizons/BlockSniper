<?php

namespace BlockHorizons\BlockSniper\cloning;

use BlockHorizons\BlockSniper\Loader;

abstract class BaseClone {
	
	const TYPE_COPY = 0;
	const TYPE_TEMPLATE = 1;
	
	public $loader;
	public $level;
	protected $name;
	
	protected $center;
	protected $radius;
	protected $height;
	
	public function __construct(Loader $loader) {
		$this->loader = $loader;
	}
	
	/**
	 * @param string $type
	 *
	 * @return bool
	 */
	public static function isCloneType(string $type): bool {
		$cloneTypeConst = strtoupper("type_" . $type);
		if(defined("self::$cloneTypeConst")) {
			return true;
		}
		return false;
	}
	
	public abstract function getName(): string;
	
	public abstract function getPermission(): string;
	
	public abstract function saveClone();
	
	public function getLoader(): Loader {
		return $this->loader;
	}
}