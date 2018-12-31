<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\parser;

use BlockHorizons\BlockSniper\exceptions\InvalidBlockException;
use pocketmine\block\Block;

class BlockParser{

	/**
	 * parse parses a string containing any amount of blocks in it, using a format with block names and tags. An example
	 * of this format is: furnace[facing=south,colour=blue],log[axis=north]. Multiple blocks may be specified by
	 * separating them with a comma, multiple tags may also be specified by separating them with a comma.
	 * parse strips all whitespace from the block string passed.
	 *
	 * @throws InvalidBlockException
	 *
	 * @param string $blockString
	 *
	 * @return Block[]
	 */
	public static function parse(string $blockString) : array {
		$blocks = [];

		$length = strlen($blockString);
		$consumer = new StringConsumer($blockString);
		while($length !== 0){
			$consumer->tryConsume();
			$length -= $consumer->getProgressedLength();
			$blocks[] = $consumer->getBlock();
		}
		return $blocks;
	}
}