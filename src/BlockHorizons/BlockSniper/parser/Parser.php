<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\parser;

use BlockHorizons\BlockSniper\exception\InvalidItemException;
use pocketmine\item\Item;

class Parser{

	/**
	 * parse parses a string containing any amount of items in it, using a format with item names and tags. An example
	 * of this format is: furnace[facing=south,colour=blue],log[axis=north]. Multiple items may be specified by
	 * separating them with a comma, multiple tags may also be specified by separating them with a comma.
	 * parse strips all whitespace from the block string passed.
	 *
	 * @param string $itemString
	 *
	 * @return Item[]
	 * @throws InvalidItemException
	 *
	 */
	public static function parse(string $itemString) : array{
		$blocks = [];

		$length = strlen($itemString);
		$consumer = new StringConsumer($itemString);
		while($length !== 0){
			$consumer->tryConsume();
			$length -= $consumer->getProgressedLength();
			$blocks[] = $consumer->getItem();
		}

		return $blocks;
	}
}