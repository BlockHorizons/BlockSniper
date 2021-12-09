<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\data;

use BlockHorizons\BlockSniper\exception\InvalidItemException;
use BlockHorizons\BlockSniper\parser\Parser;
use pocketmine\item\Item;

class BrushItem{
	/**
	 * @var string
	 * @marshal Item ID
	 */
	public $item = "golden_carrot";

	public function parse() : Item{
		$items = Parser::parse($this->item);
		if(count($items) === 0){
			throw new InvalidItemException("invalid configuration brush item");
		}

		return $items[0];
	}
}