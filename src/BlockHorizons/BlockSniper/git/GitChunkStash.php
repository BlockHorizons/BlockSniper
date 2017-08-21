<?php

namespace BlockHorizons\BlockSniper\git;


class GitChunkStash {

	/** @var array */
	private static $stash = [];
	/** @var int */
	private $stashId = 0;

	/**
	 * @param array $chunks
	 *
	 * @return int
	 */
	public function addChunkStash(array $chunks): int {
		$id = $this->stashId;
		foreach($chunks as $hash => $chunk) {
			self::$stash[$id][$hash] = $chunk;
		}
		return $id;
	}

	/**
	 * @param int $id
	 *
	 * @return bool
	 */
	public function resolveChunkStash(int $id): bool {
		if(!isset(self::$stash[$id])) {
			return false;
		}
		unset(self::$stash[$id]);
		return true;
	}
}