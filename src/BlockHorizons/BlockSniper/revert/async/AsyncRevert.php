<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\revert\async;

use BlockHorizons\BlockSniper\brush\async\BlockSniperChunkManager;
use BlockHorizons\BlockSniper\revert\Revert;
use BlockHorizons\BlockSniper\sessions\SessionManager;
use pocketmine\Server;
use pocketmine\world\format\Chunk;

abstract class AsyncRevert extends Revert{

	/** @var BlockSniperChunkManager */
	protected $manager = null;
	/** @var string[] */
	protected $modifiedChunks = [];
	/** @var int */
	protected $worldId = 0;
	/** @var string[] */
	protected $oldChunks = [];

	public function __construct(array $actualChunks, array $previousChunks, string $playerName, int $worldId){
		parent::__construct($playerName);
		$this->setModifiedChunks($actualChunks);
		$this->setModifiedChunks($previousChunks, true);
		$this->worldId = $worldId;
	}

	/**
	 * @return int
	 */
	public function getWorldId() : int{
		return $this->worldId;
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
		$player = Server::getInstance()->getPlayer($this->getPlayerName());
		if($player === null){
			// Player left the server already.
			return;
		}
		foreach($this->getOldChunks() as $hash => $chunk){
			$player->getWorld()->setChunk($chunk->getX(), $chunk->getZ(), $chunk, false);
		}
		SessionManager::getPlayerSession($player)->getRevertStore()->saveRevert($this->getDetached());
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
