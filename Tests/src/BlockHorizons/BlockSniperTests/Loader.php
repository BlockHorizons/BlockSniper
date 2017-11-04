<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockSniperTests;

use BlockHorizons\BlockSniperTests\tests\ShapeRegisteringTest;
use pocketmine\plugin\PluginBase;

class Loader extends PluginBase {

	public function onEnable() {
		/** @var Test[] $tests */
		$tests = [
			new ShapeRegisteringTest($this)
		];
		foreach($tests as $test) {
			$test->run();
		}
	}
}