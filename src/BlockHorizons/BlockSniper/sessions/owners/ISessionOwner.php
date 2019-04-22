<?php

namespace BlockHorizons\BlockSniper\sessions\owners;

interface ISessionOwner{

	/**
	 * @param string $message
	 */
	public function sendMessage(string $message) : void;
}