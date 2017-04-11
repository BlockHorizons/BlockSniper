<?php

namespace BlockHorizons\BlockSniper\cloning\types;

use BlockHorizons\BlockSniper\cloning\BaseClone;
use BlockHorizons\BlockSniper\cloning\CloneStorer;
use pocketmine\level\Level;
use pocketmine\level\Position;

class Schematic extends BaseClone {

	public function __construct(CloneStorer $cloneStorer, Level $level, bool $saveAir, Position $center, array $blocks, string $name) {
		parent::__construct($cloneStorer, $level, $saveAir, $center, $blocks, $name);
	}

	public function getName(): string {
		return "Schematic";
	}

	public function saveClone() {
		// TODO: Implement Schematics
	}
}