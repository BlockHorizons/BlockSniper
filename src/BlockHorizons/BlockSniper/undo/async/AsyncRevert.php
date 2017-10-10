<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockSniper\undo\async;

use BlockHorizons\BlockSniper\brush\async\BlockSniperChunkManager;
use BlockHorizons\BlockSniper\brush\async\tasks\RevertTask;
use BlockHorizons\BlockSniper\undo\Revert;
use pocketmine\level\format\Chunk;
use pocketmine\Server;

abstract class AsyncRevert extends Revert {

	/** @var BlockSniperChunkManager */
	protected $manager = null;
	/** @var string[] */
	protected $modifiedChunks = [];
	/** @var int */
	protected $levelId = 0;

	public function __construct(array $chunks, string $playerName, int $levelId) {
		parent::__construct($playerName);
		if(count(array_filter($chunks, function($value) {
			return is_string($value);
		})) > 0) {
			$this->modifiedChunks = $chunks;
		} else {
			$this->setModifiedChunks($chunks);
		}
		$this->levelId = $levelId;
	}

	/**
	 * @return int
	 */
	public function getLevelId(): int {
		return $this->levelId;
	}

	/**
	 * @param Chunk[] $chunks
	 *
	 * @return $this
	 */
	public function setModifiedChunks(array $chunks): self {
		foreach($chunks as $index => &$chunk) {
			$chunk = $chunk->fastSerialize();
		}
		unset($chunk);
		$this->modifiedChunks = $chunks;

		return $this;
	}

	/**
	 * @return Chunk[]
	 */
	public function getModifiedChunks(): array {
		$chunks = [];
		foreach($this->modifiedChunks as $index => $chunk) {
			$chunks[$index] = Chunk::fastDeserialize($chunk);
		}
		return $chunks;
	}

	/**
	 * @return BlockSniperChunkManager
	 */
	public function getManager(): BlockSniperChunkManager {
		return $this->manager;
	}

	/**
	 * @param BlockSniperChunkManager $manager
	 *
	 * @return $this
	 */
	public function setManager(BlockSniperChunkManager $manager): self {
		$this->manager = $manager;

		return $this;
	}

	public function restore(): void {
		Server::getInstance()->getScheduler()->scheduleAsyncTask(new RevertTask($this));
	}

	/**
	 * @return AsyncRevert
	 */
	public function getDetached(): self {
		return $this->getDetachedClass($this->modifiedChunks);
	}

	/**
	 * @param array $chunks
	 *
	 * @return AsyncRevert
	 */
	public abstract function getDetachedClass(array $chunks): self;
}