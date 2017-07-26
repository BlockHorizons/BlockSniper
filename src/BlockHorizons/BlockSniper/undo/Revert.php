<?php

namespace BlockHorizons\BlockSniper\undo;

use BlockHorizons\BlockSniper\brush\async\BlockSniperChunkManager;
use BlockHorizons\BlockSniper\brush\async\tasks\RevertTask;
use pocketmine\block\Block;
use pocketmine\level\format\Chunk;
use pocketmine\Server;

abstract class Revert {

	const TYPE_UNDO = 0;
	const TYPE_REDO = 1;

	/** @var Block[] */
	protected $blocks;
	/** @var bool */
	protected $isAsync = false;
	/** @var BlockSniperChunkManager|null */
	protected $manager = null;
	/** @var Chunk[] */
	protected $touchedChunks = [];
	/** @var bool */
	protected $scheduled = false;
	/** @var string */
	private $playerName = "";

	/**
	 * @param array                        $blocks
	 * @param BlockSniperChunkManager|null $manager
	 * @param Chunk[]                      $touchedChunks
	 * @param string                       $playerName
	 */
	public function __construct(array $blocks, BlockSniperChunkManager $manager = null, array $touchedChunks = [], string $playerName = "") {
		$this->blocks = $blocks;
		if(($this->manager = $manager) !== null) {
			$this->isAsync = true;
		}
		foreach($touchedChunks as $index => $chunk) {
			$this->touchedChunks[$index] = $chunk->fastSerialize();
		}
		$this->playerName = $playerName;
	}

	/**
	 * @param RevertTask|null $task
	 */
	public function restore(RevertTask $task = null) {
		if($this->isAsynchronous()) {
			if(!$this->scheduled) {
				$this->scheduleAsynchronous();
				return;
			}
			$processedBlocks = 0;
			$i = 0;
			foreach($this->blocks as $block) {
				if($i++ === (int) ($this->getBlockCount() / 100)) {
					$task->publishProgress(round($processedBlocks / $this->getBlockCount() * 100) . "%");
				}
				$this->getManager()->setBlockIdAt($block->x, $block->y, $block->z, $block->getId());
				$this->getManager()->setBlockDataAt($block->x, $block->y, $block->z, $block->getDamage());
				$processedBlocks++;
			}
		} else {
			foreach($this->blocks as $block) {
				$block->getLevel()->setBlock($block, $block, false, false);
			}
		}
	}

	/**
	 * @return bool
	 */
	public function isAsynchronous(): bool {
		return $this->isAsync;
	}

	/**
	 * @return bool
	 */
	public function scheduleAsynchronous(): bool {
		if(!$this->isAsynchronous()) {
			return false;
		}
		if(empty($this->touchedChunks)) {
			return false;
		}
		$this->secureAsyncBlocks();
		$this->scheduled = true;
		Server::getInstance()->getScheduler()->scheduleAsyncTask(new RevertTask($this));
		return true;
	}

	/**
	 * @param array $blocks
	 *
	 * @return $this
	 */
	public function setBlocks(array $blocks): Revert {
		$this->blocks = $blocks;

		return $this;
	}

	/**
	 * @return int
	 */
	public function getBlockCount(): int {
		return count($this->blocks);
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
	public function setManager(BlockSniperChunkManager $manager): Revert {
		$this->manager = $manager;

		return $this;
	}

	/**
	 * @return Block[]
	 */
	public function getBlocks(): array {
		return $this->blocks;
	}

	/**
	 * @return Revert
	 */
	public function getDetached(): Revert {
		$blocks = [];
		if($this->isAsynchronous()) {
			foreach($this->blocks as $block) {
				$currentBlock = Block::get($this->getManager()->getBlockIdAt($block->x, $block->y, $block->z), $this->getManager()->getBlockDataAt($block->x, $block->y, $block->z));
				$currentBlock->setComponents($block->x, $block->y, $block->z);
				$blocks[] = $currentBlock;
			}
		} else {
			foreach($this->blocks as $block) {
				$blocks[] = $block->getLevel()->getBlock($block);
			}
		}
		$this instanceof Undo ? $revert = new Redo($blocks) : $revert = new Undo($blocks);
		if($this->isAsynchronous()) {
			$revert->setAsynchronous()->setManager($this->getManager())->setPlayerName($this->getPlayerName())->setTouchedChunks($this->getTouchedChunks());
		}
		return $revert;
	}

	/**
	 * @param bool $value
	 *
	 * @return $this
	 */
	public function setAsynchronous(bool $value = true): Revert {
		$this->isAsync = $value;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getPlayerName(): string {
		return $this->playerName;
	}

	/**
	 * @param string $name
	 *
	 * @return $this
	 */
	public function setPlayerName(string $name): Revert {
		$this->playerName = $name;

		return $this;
	}

	/**
	 * @return Chunk[]
	 */
	public function getTouchedChunks(): array {
		$chunks = [];
		foreach($this->touchedChunks as $index => $chunk) {
			$chunks[$index] = Chunk::fastDeserialize($chunk);
		}
		return $chunks;
	}

	/**
	 * @param Chunk[] $chunks
	 *
	 * @return $this
	 */
	public function setTouchedChunks(array $chunks): Revert {
		foreach($chunks as $index => &$chunk) {
			$chunk = $chunk->fastSerialize();
		}
		$this->touchedChunks = $chunks;

		return $this;
	}

	/**
	 * @return $this
	 */
	public function secureAsyncBlocks(): Revert {
		foreach($this->blocks as &$block) {
			$block = Block::get($block->getId(), $block->getDamage())->setComponents($block->x, $block->y, $block->z);
		}
		return $this;
	}
}