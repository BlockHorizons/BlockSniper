<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockSniper\operation;

class FileOperationVerifier {

	/** @var bool */
	private $result = false;

	public function __construct(array $data) {
		if(
			@!is_numeric($data["uoid"]) ||
			!isset($data["operation"]) ||
			!isset($data["shape"]) ||
			!isset($data["type"]) ||
			@!is_numeric($data["size"]) ||
			!isset($data["level"]) ||
			!is_array($data["position"]) ||
			!isset($data["player"]) ||
			@!is_numeric($data["position"]["x"]) ||
			@!is_numeric($data["position"]["y"]) ||
			@!is_numeric($data["position"]["z"]) ||
			!isset($data["raw"])
		) {
			$this->result = false;
		} else {
			$this->result = true;
		}
	}

	/**
	 * @return bool
	 */
	public function getResult(): bool {
		return $this->result;
	}
}