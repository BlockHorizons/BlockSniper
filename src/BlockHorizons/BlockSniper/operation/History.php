<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockSniper\operation;

use BlockHorizons\BlockSniper\operation\exception\ClosedHistoryException;
use BlockHorizons\BlockSniper\operation\exception\InvalidFileOperationException;
use BlockHorizons\BlockSniper\operation\exception\InvalidUoidException;

class History {

	/** @var string */
	private $file = "";
	/** @var Operator */
	private $operator = null;
	/** @var int */
	private $firstUoid = 0;
	/** @var bool */
	private $isSetup = false;
	/** @var bool */
	private $closed = false;
	/** @var Operation[] */
	private $stack = [];

	public function __construct(Operator $operator, string $file) {
		$this->file = $file;
		$this->operator = $operator;
		$this->setup();
	}

	/**
	 * @return string
	 */
	public function getFile(): string {
		return $this->file;
	}

	/**
	 * @return Operator
	 */
	public function getOperator(): Operator {
		return $this->operator;
	}

	/**
	 * @param Operation $operation
	 *
	 * @return bool
	 */
	public function write(Operation $operation): bool {
		if($this->closed) {
			throw new ClosedHistoryException("Attempted to write to a closed history.");
		}
		return $operation->write();
	}

	/**
	 * @param string    $timeString
	 * @param Operation $operation
	 *
	 * @return bool
	 */
	public function push(string $timeString, Operation $operation): bool {
		$this->stack[$timeString] = $operation;
		return true;
	}

	/**
	 * @return int
	 */
	public function fetchFirstUoid(): int {
		return $this->firstUoid;
	}

	/**
	 * @return bool
	 */
	public function setup(): bool {
		if($this->closed) {
			throw new ClosedHistoryException("Attempted to setup a closed history.");
		}
		if($this->isSetup) {
			return false;
		}
		$this->initializeFile();
		$data = yaml_parse_file($this->file);
		$this->firstUoid = $data[0];
		unset($data[0]);
		foreach($data as $key => $info) {
			$this->stack[$key] = $this->getOperation($info["uoid"]);
		}
		return true;
	}

	/**
	 * @return bool
	 */
	private function initializeFile(): bool {
		if(file_exists($this->file)) {
			return false;
		}
		yaml_emit_file($this->file, [(int) 0]);
		return true;
	}

	/**
	 * @return bool
	 */
	public function isClosed(): bool {
		return $this->closed;
	}

	/**
	 * @return bool
	 */
	public function close(): bool {
		if($this->closed) {
			return false;
		}
		$data = [];
		foreach($this->stack as $date => $operation) {
			$data[$date] = $operation->getFileData();
		}
		$data[0] = $this->getOperator()->getCurrentUoid();
		yaml_emit_file($this->file, $data);
		$this->closed = true;
		return true;
	}

	/**
	 * @param int  $uoid
	 * @param bool $reconstructOnly
	 *
	 * @return Operation
	 */
	public function getOperation(int $uoid, bool $reconstructOnly = true): Operation {
		foreach($this->stack as $date => $data) {
			$data = $data->getFileData();
			if((int) $data["uoid"] === $uoid) {
				$verifier = new FileOperationVerifier($data);
				if($verifier->getResult() === false) {
					throw new InvalidFileOperationException("Invalid operation with Unique Operation ID " . $uoid . " found in " . $this->file);
				}
				$brush = unserialize($data["raw"]);
				return new Operation($this, $brush, \DateTime::createFromFormat("Y-m-D-d H:i:s", $date), $data["operation"], $uoid, $reconstructOnly);
			}
		}
		throw new InvalidUoidException("Tried to reconstruct an operation with an invalid Unique Operation ID.");
	}

	public function __destruct() {
		$this->close();
	}

	/**
	 * @param int $int
	 *
	 * @return string
	 */
	public static function typeStringFromInt(int $int) {
		switch($int) {
			default:
			case 0:
				return "Brush";
			case 1:
				return "Undo";
			case 2:
				return "Redo";
			case 3:
				return "Paste";
		}
	}
}