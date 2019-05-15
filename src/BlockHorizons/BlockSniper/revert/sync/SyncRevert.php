<?php

namespace BlockHorizons\BlockSniper\revert\sync;

use BlockHorizons\BlockSniper\data\Translation;
use BlockHorizons\BlockSniper\revert\Revert;
use pocketmine\block\Block;
use pocketmine\Server;
use pocketmine\utils\TextFormat;

abstract class SyncRevert extends Revert{

	/** @var Block[] */
	protected $blocks = [];

	public function __construct(array $blocks, string $playerName){
		parent::__construct($playerName);
		$this->blocks = $blocks;
	}

	public function restore() : void{
		$startTime = microtime(true);
		foreach($this->blocks as $block){
			$block->getWorld()->setBlock($block, $block, false);
		}
		$duration = round(microtime(true) - $startTime, 2);
		Server::getInstance()->getPlayer($this->playerName)->sendPopup(TextFormat::GREEN . Translation::get(Translation::BRUSH_STATE_DONE) . " ($duration seconds)");
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
			$blocks[] = $block->getWorld()->getBlock($block);
		}

		return $this->getDetachedClass($blocks, $this->playerName);
	}

	public abstract function getDetachedClass(array $blocks, string $playerName) : self;
}