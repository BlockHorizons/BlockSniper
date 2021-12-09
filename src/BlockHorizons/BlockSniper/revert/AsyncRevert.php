<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\revert;

use pocketmine\world\format\Chunk;
use pocketmine\world\format\io\FastChunkSerializer;
use pocketmine\world\World;

class AsyncRevert extends Revert{

	/** @var string[] */
	protected $currentlyPresentChunks = [];
	/** @var string[] */
	protected $chunksToBePlacedBack = [];

	/**
	 * @param Chunk[] $currentlyPresentChunks
	 * @param Chunk[] $chunksToBePlacedBack
	 */
	public function __construct(array $currentlyPresentChunks, array $chunksToBePlacedBack, World $world){
		parent::__construct($world);
		foreach($currentlyPresentChunks as $index => $chunk){
			$this->currentlyPresentChunks[$index] = FastChunkSerializer::serializeTerrain($chunk);
		}
		foreach($chunksToBePlacedBack as $index => $chunk){
			$this->chunksToBePlacedBack[$index] = FastChunkSerializer::serializeTerrain($chunk);
		}
	}

	/**
	 * @return Revert
	 */
	public function restore() : Revert{
		foreach($this->decodeChunks($this->chunksToBePlacedBack) as $chunkHash => $chunk){
			World::getXZ($chunkHash, $chunkX, $chunkZ);
			$this->getWorld()->setChunk($chunkX, $chunkZ, $chunk);
		}

		return new AsyncRevert($this->decodeChunks($this->chunksToBePlacedBack), $this->decodeChunks($this->currentlyPresentChunks), $this->getWorld());
	}

	/**
	 * @param string[] $rawChunks
	 *
	 * @return Chunk[]
	 */
	private function decodeChunks(array $rawChunks) : array{
		$chunks = [];
		foreach($rawChunks as $index => $chunk){
			$chunks[$index] = FastChunkSerializer::deserializeTerrain($chunk);
		}

		return $chunks;
	}
}
