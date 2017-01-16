<?php

namespace Sandertv\BlockSniper\cloning;

use Sandertv\BlockSniper\Loader;

class Template {
	
	public function __construct(Loader $owner) {
		$this->owner = $owner;
	}
	
	public function getOwner(): Loader {
		return $this->owner;
	}
	
	/*
	 * TODO
	 */
}