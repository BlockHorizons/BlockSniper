<?php

declare(strict_types=1);

namespace BlockHorizons\BlockSniper\ui\forms;

use BlockHorizons\BlockSniper\data\Translation;
use pocketmine\form\Form as FormInterface;

abstract class Form implements \JsonSerializable{

	/** @var FormInterface|null */
	protected $responseForm;
	/** @var array */
	protected $data = [];

	public function t(string $key) : string{
		return Translation::get($key);
	}

	public function setResponseForm(?FormInterface $form) : void{
		$this->responseForm = $form;
	}

	public function jsonSerialize() : array{
		return $this->data;
	}
}