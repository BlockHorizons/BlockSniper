<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\parser;

use BlockHorizons\BlockSniper\exceptions\InvalidBlockException;
use pocketmine\block\Block;
use pocketmine\item\Item;
use function preg_match;
use function preg_replace;
use function sprintf;
use function strlen;
use function substr;

class StringConsumer{

	/** @var string */
	private $blockString = "";

	/** @var Block|null */
	private $block = null;
	/** @var BlockTag[] */
	private $tags = [];

	/** @var int */
	private $blockLength = 0;

	/**
	 * StringConsumer constructor.
	 *
	 * @param string $blockString with possibly multiple blocks in it. The block string has all its whitespace removed.
	 */
	public function __construct(string $blockString){
		$this->blockString = preg_replace("/\\s/", "", $blockString);
	}

	/**
	 * tryConsume tries to consume the block string, by consuming the block name and its tags. Each time this method is
	 * called, only one block is consumed. The block parsed can be retrieved using StringConsumer->getBlock() after
	 * StringConsumer->tryConsume(), the tags parsed using StringConsumer->getTags() and the length of the last parsed
	 * blocks + tags using StringConsumer->getProgressedLength().
	 *
	 * @throws InvalidBlockException
	 */
	public function tryConsume() {
		$this->blockLength = 0;
		$this->tryConsumeBlock();
		$this->tryConsumeTags();
		if(isset($this->blockString[0]) && $this->blockString[0] === ","){
			$this->blockString = substr($this->blockString, 1);
			$this->blockLength++;
		}
	}

	/**
	 * getBlock returns the block parsed by the string consumer. Null is returned if the string has not yet been
	 * consumed, or if an exception was thrown during the parsing.
	 *
	 * @return Block
	 */
	public function getBlock() : ?Block{
		return $this->block;
	}

	/**
	 * getTags returns an array of tags successfully parsed by the string consumer.
	 *
	 * @return BlockTag[]
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
	 * tryConsumeBlock tries to consume a block from the block string. If successful, the block string is advanced and
	 * the block is set.
	 *
	 * @throws InvalidBlockException
	 */
	private function tryConsumeBlock() {
		$blockName = "";
		$length = strlen($this->blockString);
		for($i = 0; $i < $length; $i++) {
			$char = $this->blockString[$i];
			$match = preg_match('/[a-zA-Z_]/', $char);
			if($match === 0) {
				// Not a letter. If the character is a [, it probably indicates the start of the block tags.
				if($char !== "[") {
					throw new InvalidBlockException(sprintf("cannot parse %s as block: invalid character %s at offset %s",
															$this->blockString, $char, $i));
				}
				break;
			} elseif($match === false){
				throw new InvalidBlockException(sprintf("cannot parse %s as block: error matching: %s", $this->blockString, preg_last_error()));
			}
			$blockName .= $char;
		}
		$this->blockString = substr($this->blockString, strlen($blockName));
		$this->blockLength += strlen($blockName);

		// Parse the block name. (Might also be an ID)
		$this->block = $this->parseBlockName($blockName);
	}

	/**
	 * parseBlockName parses a block name and creates a block instance. The block name might also be an ID, in which
	 * case it will be resolved too.
	 *
	 * @throws InvalidBlockException
	 *
	 * @param string $name
	 *
	 * @return Block
	 */
	private function parseBlockName(string $name) : Block {
		try {
			return Item::fromString($name)->getBlock();
		}catch(\InvalidArgumentException $exception){
			throw new InvalidBlockException(sprintf("cannot parse %s as block: block not found", $name));
		}
	}

	/**
	 * tryConsumeTags attempts to parse the leftover block string as block tags, provided they are enclosed in brackets.
	 *
	 * @throws InvalidBlockException
	 */
	private function tryConsumeTags() {
		if(strlen($this->blockString) === 0) {
			// There are no tags available. Don't do anything.
			return;
		}
		// Remove the first bracket from the string.
		$this->blockString = substr($this->blockString, 1);
		$this->blockLength++;

		while(true) {
			$tagName = "";
			$tagValue = "";
			$nameComplete = false;
			if(($length = strlen($this->blockString)) === 0) {
				return;
			}
			for($i = 0; $i < $length; $i++) {
				$char = $this->blockString[$i];
				$match = preg_match('/[a-zA-Z0-9]/', $char);
				if($match === 0) {
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
							throw new InvalidBlockException(sprintf("cannot parse %s as tag: invalid character %s at offset %s", $this->blockString, $char, $i));
					}
				} elseif($match === false){
					throw new InvalidBlockException(sprintf("cannot parse %s as tag: error matching: %s", $this->blockString, preg_last_error()));
				}
				if(!$nameComplete){
					$tagName .= $char;
					continue;
				}
				$tagValue .= $char;
			}
			tag_finalize:
			if(!isset($this->blockString[$i])){
				throw new InvalidBlockException(sprintf("cannot parse %s as tag: expected ']' or ',', but found none at offset %s", $this->blockString, $i));
			}
			$lastChar = $this->blockString[$i];

			// Progress one further in the string to get rid of the unneeded bracket or comma.
			$this->blockString = substr($this->blockString, ++$i);
			$this->tags[] = new BlockTag($tagName, $tagValue);
			$this->blockLength += $i;

			if($lastChar === "]"){
				// The last character was a bracket, so end the loop.
				return;
			} elseif($lastChar != ","){
				throw new InvalidBlockException(sprintf("cannot parse %s as tag: invalid character %s at offset %s", $this->blockString, $lastChar, 0));
			}
		}
	}
}