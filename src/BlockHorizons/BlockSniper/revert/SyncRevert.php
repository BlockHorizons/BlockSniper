<?php

namespace BlockHorizons\BlockSniper\revert;

use pocketmine\block\Block;
use pocketmine\world\World;

class SyncRevert extends Revert{

	/** @var Block[] */
	protected $blocks = [];

	public function __construct(array $blocks, World $world){
		parent::__construct($world);
		$this->blocks = $blocks;
	}

	/**
	 * @return Revert
	 */
	public function restore() : Revert{
		$oldBlocks = [];
		foreach($this->blocks as $block){
			$oldBlocks[] = $this->getWorld()->getBlock($block);
			$this->getWorld()->setBlock($block, $block, false);
		}

		return new SyncRevert($oldBlocks, $this->getWorld());
	}
}