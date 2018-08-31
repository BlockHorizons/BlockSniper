<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\revert\async;

use BlockHorizons\BlockSniper\brush\async\BlockSniperChunkManager;
use BlockHorizons\BlockSniper\brush\async\tasks\RevertTask;
use BlockHorizons\BlockSniper\revert\Revert;
use pocketmine\level\format\Chunk;
use pocketmine\Server;

abstract class AsyncRevert extends Revert{

	/** @var BlockSniperChunkManager */
	protected $manager = null;
	/** @var string[] */
	protected $modifiedChunks = [];
	/** @var int */
	protected $levelId = 0;
	/** @var string[] */
	protected $oldChunks = [];

	public function __construct(array $actualChunks, array $previousChunks, string $playerName, int $levelId){
		parent::__construct($playerName);
		$this->setModifiedChunks($actualChunks);
		$this->setModifiedChunks($previousChunks, true);
		$this->levelId = $levelId;
	}

	/**
	 * @return int
	 */
	public function getLevelId() : int{
		return $this->levelId;
	}

	/**
	 * @return Chunk[]
	 */
	public function getModifiedChunks() : array{
		$chunks = [];
		foreach($this->modifiedChunks as $index => $chunk){
			$chunks[$index] = Chunk::fastDeserialize($chunk);
		}

		return $chunks;
	}

	/**
	 * @param Chunk[] $chunks
	 * @param bool    $oldChunks
	 *
	 * @return $this
	 */
	public function setModifiedChunks(array $chunks, bool $oldChunks = false) : self{
		foreach($chunks as $index => &$chunk){
			$chunk = $chunk->fastSerialize();
		}
		unset($chunk);
		if($oldChunks){
			$this->oldChunks = $chunks;
		}else{
			$this->modifiedChunks = $chunks;
		}

		return $this;
	}

	/**
	 * @return Chunk[]
	 */
	public function getOldChunks() : array{
		$chunks = [];
		foreach($this->oldChunks as $index => $chunk){
			$chunks[$index] = Chunk::fastDeserialize($chunk);
		}

		return $chunks;
	}

	/**
	 * @return BlockSniperChunkManager
	 */
	public function getManager() : BlockSniperChunkManager{
		return $this->manager;
	}

	/**
	 * @param BlockSniperChunkManager $manager
	 *
	 * @return $this
	 */
	public function setManager(BlockSniperChunkManager $manager) : self{
		$this->manager = $manager;

		return $this;
	}

	public function restore() : void{
		Server::getInstance()->getAsyncPool()->submitTask(new RevertTask($this));
	}

	/**
	 * @return AsyncRevert
	 */
	public function getDetached() : Revert{
		return $this->getDetachedClass();
	}

	/**
	 * @return AsyncRevert
	 */
	public abstract function getDetachedClass() : self;
}
