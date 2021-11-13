<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\parser;

use BlockHorizons\BlockSniper\exception\InvalidItemException;
use InvalidArgumentException;
use pocketmine\item\Item;
use pocketmine\item\LegacyStringToItemParser;
use pocketmine\item\LegacyStringToItemParserException;
use pocketmine\item\StringToItemParser;
use function preg_match;
use function preg_replace;
use function sprintf;
use function strlen;
use function substr;

class StringConsumer{

	/** @var string */
	private $itemString = "";

	/** @var Item|null */
	private $item = null;
	/** @var Tag[] */
	private $tags = [];

	/** @var int */
	private $blockLength = 0;

	/**
	 * StringConsumer constructor.
	 *
	 * @param string $itemString with possibly multiple blocks in it. The item string has all its whitespace removed.
	 */
	public function __construct(string $itemString){
		$this->itemString = preg_replace("/\\s/", "", $itemString);
	}

	/**
	 * tryConsume tries to consume the block string, by consuming the block name and its tags. Each time this method is
	 * called, only one block is consumed. The block parsed can be retrieved using StringConsumer->getBlock() after
	 * StringConsumer->tryConsume(), the tags parsed using StringConsumer->getTags() and the length of the last parsed
	 * blocks + tags using StringConsumer->getProgressedLength().
	 *
	 * @throws InvalidItemException
	 */
	public function tryConsume() : void{
		$this->blockLength = 0;
		$this->tryConsumeItem();
		$this->tryConsumeTags();
		if(isset($this->itemString[0]) && $this->itemString[0] === ","){
			$this->itemString = substr($this->itemString, 1);
			$this->blockLength++;
		}
	}

	/**
	 * getItem returns the item parsed by the string consumer. Null is returned if the string has not yet been
	 * consumed, or if an exception was thrown during the parsing.
	 *
	 * @return Item
	 */
	public function getItem() : ?Item{
		return $this->item;
	}

	/**
	 * getTags returns an array of tags successfully parsed by the string consumer.
	 *
	 * @return Tag[]
	 */
	public function getTags() : array{
		return $this->tags;
	}

	/**
	 * getProcessedLength returns the progressed length in the string that is being consumed. It is the length that has
	 * been consumed since the last time tryConsume() was called.
	 *
	 * @return int
	 */
	public function getProgressedLength() : int{
		return $this->blockLength;
	}

	/**
	 * tryConsumeBlock tries to consume an item from the block string. If successful, the item string is advanced and
	 * the block is set.
	 *
	 * @throws InvalidItemException
	 */
	private function tryConsumeItem() : void{
		$blockName = "";
		$length = strlen($this->itemString);
		for($i = 0; $i < $length; $i++){
			$char = $this->itemString[$i];
			$match = preg_match('/[a-zA-Z0-9_:]/', $char);
			if($match === 0){
				// Not a letter. If the character is a [, it probably indicates the start of the block tags. If it is a
				// comma, it means we have another block following.
				if($char !== "[" && $char !== ","){
					throw new InvalidItemException(sprintf("cannot parse %s as block: invalid character %s at offset %s",
							$this->itemString, $char, $i
						)
					);
				}
				break;
			}elseif($match === false){
				throw new InvalidItemException(sprintf("cannot parse %s as block: error matching: %s", $this->itemString, preg_last_error()));
			}
			$blockName .= $char;
		}
		$this->itemString = substr($this->itemString, strlen($blockName));
		$this->blockLength += strlen($blockName);

		// Parse the item name. (Might also be an ID)
		$this->item = $this->parseItemName($blockName);
	}

	/**
	 * parseItemName parses an item name and creates an item instance. The item name might also be an ID, in which
	 * case it will be resolved too.
	 *
	 * @param string $name
	 *
	 * @return Item
	 * @throws InvalidItemException
	 *
	 */
	private function parseItemName(string $name) : Item{
		if(($item = StringToItemParser::getInstance()->parse($name)) !== null){
			return $item;
		}
		try{
			return LegacyStringToItemParser::getInstance()->parse($name);
		}catch(LegacyStringToItemParserException $exception){
			throw new InvalidItemException(sprintf("cannot parse %s as block: block not found", $name));
		}
	}

	/**
	 * tryConsumeTags attempts to parse the leftover block string as block tags, provided they are enclosed in brackets.
	 *
	 * @throws InvalidItemException
	 */
	private function tryConsumeTags() : void{
		if(strlen($this->itemString) === 0 || $this->itemString[0] !== "["){
			// There are no tags available. Don't do anything.
			return;
		}
		// Remove the first bracket from the string.
		$this->itemString = substr($this->itemString, 1);
		$this->blockLength++;

		while(true){
			$tagName = "";
			$tagValue = "";
			$nameComplete = false;
			if(($length = strlen($this->itemString)) === 0){
				return;
			}
			for($i = 0; $i < $length; $i++){
				$char = $this->itemString[$i];
				$match = preg_match('/[a-zA-Z0-9]/', $char);
				if($match === 0){
					// Not a letter or number. If the character is a =, it indicates the tag's value, if it is a ], it
					// indicates the end of the string and if it's a , it indicates the end of the tag.
					switch($char){
						case "]":
							if($nameComplete){
								goto tag_finalize;
							}
						case ",":
							if($nameComplete){
								goto tag_finalize;
							}
						case "=":
							if(!$nameComplete){
								$nameComplete = true;
								continue 2;
							}
						default:
							throw new InvalidItemException(sprintf("cannot parse %s as tag: invalid character %s at offset %s", $this->itemString, $char, $i));
					}
				}elseif($match === false){
					throw new InvalidItemException(sprintf("cannot parse %s as tag: error matching: %s", $this->itemString, preg_last_error()));
				}
				if(!$nameComplete){
					$tagName .= $char;
					continue;
				}
				$tagValue .= $char;
			}
			tag_finalize:
			if(!isset($this->itemString[$i])){
				throw new InvalidItemException(sprintf("cannot parse %s as tag: expected ']' or ',', but found none at offset %s", $this->itemString, $i));
			}
			$lastChar = $this->itemString[$i];

			// Progress one further in the string to get rid of the unneeded bracket or comma.
			$this->itemString = substr($this->itemString, ++$i);
			$this->tags[] = new Tag($tagName, $tagValue);
			$this->blockLength += $i;

			if($lastChar === "]"){
				// The last character was a bracket, so end the loop.
				return;
			}elseif($lastChar != ","){
				throw new InvalidItemException(sprintf("cannot parse %s as tag: invalid character %s at offset %s", $this->itemString, $lastChar, 0));
			}
		}
	}
}