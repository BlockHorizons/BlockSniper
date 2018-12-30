<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniperTests\tests;

use BlockHorizons\BlockSniper\brush\BaseShape;
use BlockHorizons\BlockSniper\brush\Brush;
use pocketmine\math\AxisAlignedBB;
use pocketmine\math\Vector3;

class TestShape extends BaseShape{

	public function getBlocksInside(bool $vectorOnly = false) : \Generator{
		yield null;
	}

	public function getName() : string{
		return "Test Shape";
	}

	/**
	 * @param Vector3       $center
	 * @param Brush         $brush
	 * @param AxisAlignedBB $bb
	 */
	public function buildSelection(Vector3 $center, Brush $brush, AxisAlignedBB $bb) : void{

	}

	/**
	 * @return int
	 */
	public function getBlockCount() : int{
		return 0;
	}
}