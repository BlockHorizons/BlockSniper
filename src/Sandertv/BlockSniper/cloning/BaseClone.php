<?php

namespace Sandertv\BlockSniper\cloning;

use Sandertv\BlockSniper\Loader;

abstract class BaseClone {
	
	const TYPE_COPY = 0;
	const TYPE_TEMPLATE = 1;
	
	public $owner;
	
	public function __construct(Loader $owner) {
		$this->owner = $owner;
	}
	
	public abstract function getName(): string;
	
	public abstract function getPermission(): string;
	
	public abstract function saveClone();
	
	public function getOwner(): Loader {
		return $this->owner;
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
}