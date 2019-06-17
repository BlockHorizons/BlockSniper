<?php

namespace BlockHorizons\BlockSniper\session\owner;

interface ISessionOwner{

	/**
	 * @param string $message
	 */
	public function sendMessage(string $message) : void;
}