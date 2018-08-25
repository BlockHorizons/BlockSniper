<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniperTests\tests;

use BlockHorizons\BlockSniper\brush\BaseShape;

class TestShape extends BaseShape{

	public function getBlocksInside(bool $vectorOnly = false) : array{
		return [];
	}

	public function getApproximateProcessedBlocks() : int{
		return 0;
	}

	public function getName() : string{
		return "Test Shape";
	}

	public function getTouchedChunks() : array{
		return [];
	}
}