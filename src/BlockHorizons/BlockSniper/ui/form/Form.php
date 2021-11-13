<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\ui\form;

use BlockHorizons\BlockSniper\data\Translation;
use JsonSerializable;
use pocketmine\form\Form as FormInterface;
use pocketmine\player\Player;

abstract class Form implements JsonSerializable{

	/** @var FormInterface|null */
	protected $responseForm;
	/** @var mixed[] */
	protected $data = [];

	public function t(string $key) : string{
		return Translation::get($key);
	}

	public function setResponseForm(?FormInterface $form) : void{
		$this->responseForm = $form;
	}

	/**
	 * @return mixed[]
	 */
	public function jsonSerialize() : array{
		return $this->data;
	}

	public function onSubmit(Player $player) : void{

	}
}