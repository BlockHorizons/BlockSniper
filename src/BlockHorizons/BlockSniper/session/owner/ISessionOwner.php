<?php

namespace BlockHorizons\BlockSniper\session\owner;

interface ISessionOwner{

	public function getName() : string;

	/**
	 * @param string $message
	 */
	public function sendMessage(string $message) : void;
}