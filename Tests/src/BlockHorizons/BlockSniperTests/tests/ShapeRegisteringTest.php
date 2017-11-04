<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockSniperTests\tests;

use BlockHorizons\BlockSniper\brush\BaseShape;
use BlockHorizons\BlockSniper\brush\registration\ShapeRegistration;
use BlockHorizons\BlockSniperTests\Test;

class ShapeRegisteringTest extends Test {

	public function onRun(): bool {
		ShapeRegistration::registerShape(TestShape::class, 5);
		$shape = ShapeRegistration::getShape("test");
		return $shape instanceof BaseShape;
	}
}