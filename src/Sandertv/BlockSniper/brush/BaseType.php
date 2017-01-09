<?php

namespace Sandertv\BlockSniper\brush;

use Sandertv\BlockSniper\Loader;

abstract class BaseType {
	
	public function __construct(Loader $main) {
		$this->main = $main;
	}
	
	const MAX_WORLD_HEIGHT = 256;
	const MIN_WORLD_HEIGHT = 0;
	
	const TYPE_OVERLAY = 1;
	const TYPE_LAYER = 2, TYPE_FLAT_LAYER = 2;
	const TYPE_REPLACE = 3;
	const TYPE_FLATTEN = 4, TYPE_EQUALIZE = 4;
	const TYPE_DRAIN = 5;
	const TYPE_LEAF_BLOWER = 6;
	const TYPE_CLEAN = 7, TYPE_CLEAR = 7;
	
	public abstract function getName(): string;
	
	public abstract function getPermission(): string;
	
	public abstract function fillShape(): bool;
	
	public function getMain(): Loader {
		return $this->main;
	}
}
