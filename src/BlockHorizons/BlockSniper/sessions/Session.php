<?php

namespace BlockHorizons\BlockSniper\sessions;

use BlockHorizons\BlockSniper\brush\Brush;
use BlockHorizons\BlockSniper\cloning\CloneStorer;
use BlockHorizons\BlockSniper\data\Translation;
use BlockHorizons\BlockSniper\Loader;
use BlockHorizons\BlockSniper\sessions\owners\ISessionOwner;
use BlockHorizons\BlockSniper\sessions\owners\PlayerSessionOwner;
use BlockHorizons\BlockSniper\sessions\owners\ServerSessionOwner;
use BlockHorizons\BlockSniper\undo\RevertStorer;
use pocketmine\utils\TextFormat;

abstract class Session {

	/** @var ISessionOwner */
	protected $sessionOwner = null;
	/** @var string */
	protected $dataFile = "";
	/** @var Brush */
	protected $brush = null;
	/** @var RevertStorer */
	protected $revertStorer = null;
	/** @var CloneStorer */
	protected $cloneStorer = null;

	public function __construct(ISessionOwner $sessionOwner, Loader $loader) {
		$this->sessionOwner = $sessionOwner;
		$this->revertStorer = new RevertStorer($loader->getSettings()->getMaxUndoStores());
		$this->cloneStorer = new CloneStorer($this, $loader->getDataFolder());

		if($this->initializeBrush()) {
			$loader->getLogger()->debug(TextFormat::GREEN . (new Translation(Translation::LOG_BRUSH_RESTORED, [$this->getSessionOwner()->getName()]))->getMessage());
		}
	}

	/**
	 * @return PlayerSessionOwner|ServerSessionOwner
	 */
	public function getSessionOwner(): ISessionOwner {
		return $this->sessionOwner;
	}

	/**
	 * @return string
	 */
	public function getDataFile(): string {
		return $this->dataFile;
	}

	/**
	 * @return Brush
	 */
	public function getBrush(): Brush {
		return $this->brush;
	}

	/**
	 * @return RevertStorer
	 */
	public function getRevertStorer(): RevertStorer {
		return $this->revertStorer;
	}

	/**
	 * @return CloneStorer
	 */
	public function getCloneStorer(): CloneStorer {
		return $this->cloneStorer;
	}

	/**
	 * @return bool
	 */
	protected abstract function initializeBrush(): bool;
}