<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniperTests;

use BlockHorizons\BlockSniperTests\tests\ShapeRegistrationTest;
use pocketmine\plugin\PluginBase;

class Loader extends PluginBase{

	public function onEnable(){
		/** @var Test[] $tests */
		$tests = [
			new ShapeRegistrationTest($this)
		];
		foreach($tests as $test){
			$test->run();
		}
	}
}