<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\parser;

use pocketmine\block\Block;

class Tag{

	/** @var string */
	private $name = "";
	/** @var string */
	private $value;

	public function __construct(string $name, string $value){
		$this->name = $name;
		$this->value = $value;
	}

	/**
	 * getName returns the name of a tag.
	 *
	 * @return string
	 */
	public function getName() : string{
		return $this->name;
	}

	/**
	 * getValue returns the raw value this tag holds.
	 *
	 * @return string
	 */
	public function getValue() : string{
		return $this->value;
	}

	/**
	 * apply applies this tag on a block passed. An exception is thrown if the block does not have a field with the
	 * correct tag name or tag value type.
	 *
	 * @param Block $block
	 */
	public function apply(Block $block) : void{
		// TODO: Implement this when block properties are finalized in PocketMine.
	}
}