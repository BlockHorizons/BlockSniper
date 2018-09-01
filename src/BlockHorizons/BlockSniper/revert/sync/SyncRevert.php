<?php

namespace BlockHorizons\BlockSniper\revert\sync;

use BlockHorizons\BlockSniper\revert\Revert;
use pocketmine\block\Block;

abstract class SyncRevert extends Revert{

	/** @var Block[] */
	protected $blocks = [];

	public function __construct(array $blocks, string $playerName){
		parent::__construct($playerName);
		$this->blocks = $blocks;
	}

	public function restore() : void{
		foreach($this->blocks as $block){
			$block->getLevel()->setBlock($block, $block, false, false);
		}
	}

	/**
	 * @return int
	 */
	public function getBlockCount() : int{
		return count($this->blocks);
	}

	/**
	 * @return Block[]
	 */
	public function getBlocks() : array{
		return $this->blocks;
	}

	/**
	 * @param array $blocks
	 *
	 * @return $this
	 */
	public function setBlocks(array $blocks) : SyncRevert{
		$this->blocks = $blocks;

		return $this;
	}

	/**
	 * @return SyncRevert
	 */
	public function getDetached() : Revert{
		$blocks = [];
		foreach($this->blocks as $block){
			$blocks[] = $block->getLevel()->getBlock($block);
		}

		return $this->getDetachedClass($blocks, $this->playerName);
	}

	public abstract function getDetachedClass(array $blocks, string $playerName) : self;
}