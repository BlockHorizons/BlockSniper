<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockSniper\operation;

use BlockHorizons\BlockSniper\brush\Brush;
use BlockHorizons\BlockSniper\operation\exception\UnsignedOperationException;

class Operation {

	const OPERATION_TYPE_BRUSH = 0;
	const OPERATION_TYPE_UNDO = 1;
	const OPERATION_TYPE_REDO = 2;
	const OPERATION_TYPE_PASTE = 3;

	/** @var Brush */
	private $brush = null;
	/** @var \DateTime */
	private $executionTime = null;
	/** @var string */
	private $file = "";
	/** @var bool */
	private $written = false;
	/** @var History */
	private $history = null;
	/** @var int */
	private $uoid = 0;
	/** @var int */
	private $type = 0;
	/** @var array */
	private $inputData = [];

	public function __construct(History $history, Brush $brush, \DateTime $executionTime, int $type = self::OPERATION_TYPE_BRUSH, int $uoid = 0, bool $reconstructed = false) {
		$this->sign($uoid);
		if($reconstructed) {
			$this->written = true;
		}
		$this->history = $history;
		$this->brush = $brush;
		$this->executionTime = $executionTime;
		$this->file = $history->getFile();
		$this->type = $type;
	}

	/**
	 * @return History
	 */
	public function getHistory(): History {
		return $this->history;
	}

	/**
	 * @return int
	 */
	public function getUniqueOperationId(): int {
		if(!$this->isSigned()) {
			throw new UnsignedOperationException("Tried to retrieve Unique Operation ID from an unsigned operation.");
		}
		return $this->uoid;
	}

	/**
	 * @param int $uoid
	 *
	 * @return bool
	 */
	public function sign(int $uoid = 0): bool {
		if($uoid === 0) {
			$uoid = $this->getHistory()->getOperator()->generateUniqueOperationId();
		}
		if($this->isSigned()) {
			return false;
		}
		$this->uoid = $uoid;
		return true;
	}

	/**
	 * @return bool
	 */
	public function isSigned(): bool {
		return $this->uoid !== 0;
	}

	/**
	 * @return Brush
	 */
	public function getBrush(): Brush {
		return $this->brush;
	}

	/**
	 * @return \DateTime
	 */
	public function getExecutionTime(): \DateTime {
		return $this->executionTime;
	}

	/**
	 * Returns a boolean indicating success. Each operation can only be written once.
	 *
	 * @return bool
	 */
	public function write(): bool {
		if($this->written) {
			return false;
		}
		$this->written = true;

		$formattedDate = $this->executionTime->format("Y-m-D-d H:i:s");
		$center = $this->brush->getShape()->getCenter();
		$this->inputData = [
			"uoid" => $this->uoid,
			"operation" => History::typeStringFromInt($this->type),
			"player" => $this->brush->getPlayerName(),
			"shape" => $this->brush->getShape()->getName(),
			"type" => $this->brush->getType()->getName(),
			"size" => $this->brush->getSize(),
			"level" => $this->brush->getShape()->getLevel()->getName(),
			"position" => [
				$center->x,
				$center->y,
				$center->z
			],
			"raw" => serialize(clone $this->brush)
		];
		$this->history->push($formattedDate, $this);
		return true;
	}

	/**
	 * @return array
	 */
	public function getFileData(): array {
		return $this->inputData;
	}

	/**
	 * @return bool
	 */
	public function isWritten(): bool {
		return $this->written;
	}

	/**
	 * @return int
	 */
	public function getType(): int {
		return $this->type;
	}

	public function collidesWith(Operation $operation): bool {
		unset($operation);
		return false;
	}
}