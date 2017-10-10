<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockSniper\git;

use pocketmine\level\format\Chunk;

class GitCommit {

	/** @var Chunk[] */
	private $chunks = [];

	public function __construct(array $chunks) {
		$this->chunks = $chunks;
	}

	/**
	 * @return Chunk[]
	 */
	public function getChunks(): array {
		return $this->chunks;
	}
}