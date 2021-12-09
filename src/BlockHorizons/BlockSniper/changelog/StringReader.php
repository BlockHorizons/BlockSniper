<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\changelog;

class StringReader{

	/** @var string */
	private $data;

	public function __construct(string $data){
		$this->data = $data;
	}

	/**
	 * @param string $pattern
	 *
	 * @return string
	 */
	public function readUntil(string $pattern) : string{
		$pos = strpos($this->data, $pattern);
		if($pos === false){
			return "";
		}
		$sub = substr($this->data, 0, $pos);
		$this->data = substr($this->data, $pos + strlen($pattern));

		return $sub;
	}

	/**
	 * @param string $pattern
	 *
	 * @return bool
	 */
	public function canReadUntil(string $pattern) : bool{
		return strpos($this->data, $pattern) !== false;
	}

	/**
	 * @return string
	 */
	public function remaining() : string{
		return $this->data;
	}
}