<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\brush;

class TreeProperties{
	/** @var string */
	public $trunkBlocks = "";
	/** @var string */
	public $leavesBlocks = "";
	/** @var int */
	public $trunkHeight = 20;
	/** @var int */
	public $trunkWidth = 2;
	/** @var int */
	public $maxBranchLength = 8;
	/** @var int */
	public $leavesClusterSize = 7;
}