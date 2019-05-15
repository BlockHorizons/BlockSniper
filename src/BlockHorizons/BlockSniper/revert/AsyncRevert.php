<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\revert;

use pocketmine\world\format\Chunk;
use pocketmine\world\World;

class AsyncRevert extends Revert{

	/** @var string[] */
	protected $currentlyPresentChunks = [];
	/** @var string[] */
	protected $chunksToBePlacedBack = [];

	public function __construct(array $currentlyPresentChunks, array $chunksToBePlacedBack, World $world){
		parent::__construct($world);
		foreach($currentlyPresentChunks as $index => $chunk){
			$this->currentlyPresentChunks[] = $chunk->fastSerialize();
		}
		foreach($chunksToBePlacedBack as $index => $chunk){
			$this->chunksToBePlacedBack[] = $chunk->fastSerialize();
		}
	}

	/**
	 * @return Revert
	 */
	public function restore() : Revert{
		foreach($this->decodeChunks($this->chunksToBePlacedBack) as $chunk){
			$this->getWorld()->setChunk($chunk->getX(), $chunk->getZ(), $chunk, false);
		}

		return new AsyncRevert($this->decodeChunks($this->chunksToBePlacedBack), $this->decodeChunks($this->currentlyPresentChunks), $this->getWorld());
	}

	/**
	 * @param array $rawChunks
	 *
	 * @return array
	 */
	private function decodeChunks(array $rawChunks) : array{
		$chunks = [];
		foreach($rawChunks as $index => $chunk){
			$chunks[$index] = Chunk::fastDeserialize($chunk);
		}

		return $chunks;
	}
}
